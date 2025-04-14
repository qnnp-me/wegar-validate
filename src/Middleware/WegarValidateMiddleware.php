<?php

namespace Wegar\Validate\Middleware;

use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use support\exception\PageNotFoundException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use Wegar\Validate\Annotation\Method\CONNECT;
use Wegar\Validate\Annotation\Method\DELETE;
use Wegar\Validate\Annotation\Method\GET;
use Wegar\Validate\Annotation\Method\HEAD;
use Wegar\Validate\Annotation\Method\OPTIONS;
use Wegar\Validate\Annotation\Method\PATCH;
use Wegar\Validate\Annotation\Method\POST;
use Wegar\Validate\Annotation\Method\PUT;
use Wegar\Validate\Annotation\Method\TRACE;
use Wegar\Validate\Helper\MethodHelper;

class WegarValidateMiddleware implements MiddlewareInterface
{
  protected array $attrs = [
    CONNECT::class,
    DELETE::class,
    GET::class,
    HEAD::class,
    OPTIONS::class,
    PATCH::class,
    POST::class,
    PUT::class,
    TRACE::class
  ];

  public function process(Request $request, callable $handler): Response
  {
    $callback = $this->getCallback();
    $use_notfound = !config('plugin.wegar.validate.app.throw', false);
    if (is_array($callback) && count($callback) === 2) {
      $class = $callback[0];
      if (class_exists($class)) {
        $this->validateClassMethod($class, $callback[1], $use_notfound);
      }
    } else {
      $this->validateFunction($callback, $use_notfound);
    }

    return $handler($request);
  }

  protected function getCallback(): callable
  {
    return request()->route?->getCallback() ?: [request()->controller, request()->action];
  }

  protected function validateClassMethod(string $class, string $method, bool $use_notfound): void
  {
    try {
      $reflection = new ReflectionClass($class);
    } catch (ReflectionException $e) {
      $this->handleError(true);
    }
    if ($reflection->hasMethod($method)) {
      $methodRef = $reflection->getMethod($method);
      if (!$this->checkMethod($methodRef)) {
        $this->handleError($use_notfound);
      }
    } else {
      $this->handleError(true);
    }
  }

  protected function validateFunction(callable $callback, bool $use_notfound): void
  {
    try {
      $functionRef = new ReflectionFunction($callback);
    } catch (ReflectionException $e) {
      $this->handleError(true);
    }
    if (!$this->checkMethod($functionRef)) {
      $this->handleError($use_notfound);
    }
  }

  protected function handleError(bool $use_notfound)
  {
    if ($use_notfound) throw new PageNotFoundException();
    throw new PageNotFoundException('Method not allowed', 405);
  }

  protected function checkMethod(ReflectionMethod|ReflectionFunction $action_ref): bool
  {
    $method_force = config('plugin.wegar.validate.app.force', true);
    $method_matched = false;
    foreach ($action_ref->getAttributes() as $attribute) {
      $attr_name = $attribute->getName();
      if (in_array($attr_name, $this->attrs + config('plugin.wegar.validate.app.methods', []))) {
        $instance = $attribute->newInstance();
        if (!property_exists($instance, 'name')) {
          continue;
        }
        if ($instance->name === request()->method()) {
          $method_matched = true;
          if ($instance instanceof MethodHelper) {
            $instance->validate($action_ref);
          }
          continue;
        }
        $method_force = true;
      }
    }
    return $method_matched || !$method_force;
  }
}