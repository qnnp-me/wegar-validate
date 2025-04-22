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
    $callback = $this->getCallback($request) ?? $handler;
    $use_notfound = !config('plugin.wegar.validate.app.throw', false);
    if (is_array($callback) && count($callback) === 2) {
      $class = $callback[0];
      if (class_exists($class)) {
        $this->validateClassMethod($request, $class, $callback[1], $use_notfound);
      }
    } else {
      $this->validateFunction($request, $callback, $use_notfound);
    }

    return $handler($request);
  }

  protected function getCallback(Request $request): callable|array|null
  {
    return $request->route?->getCallback() ?: ($request->controller ? [$request->controller, $request->action] : null);
  }

  protected function validateClassMethod(Request $request, string $class, string $method, bool $use_notfound): void
  {
    try {
      $reflection = new ReflectionClass($class);
    } catch (ReflectionException $e) {
      $this->handleError(true);
    }
    if ($reflection->hasMethod($method)) {
      $methodRef = $reflection->getMethod($method);
      if (!$this->checkMethod($request, $methodRef)) {
        $this->handleError($use_notfound);
      }
    } else {
      $this->handleError(true);
    }
  }

  protected function validateFunction(Request $request, callable $callback, bool $use_notfound): void
  {
    try {
      $functionRef = new ReflectionFunction($callback);
    } catch (ReflectionException $e) {
      $this->handleError(true);
    }
    if (!$this->checkMethod($request, $functionRef)) {
      $this->handleError($use_notfound);
    }
  }

  protected function handleError(bool $use_notfound)
  {
    if ($use_notfound) throw new PageNotFoundException();
    throw new PageNotFoundException('Method not allowed', 405);
  }

  protected function checkMethod(Request $request, ReflectionMethod|ReflectionFunction $action_ref): bool
  {
    $method_force = config('plugin.wegar.validate.app.force', true);
    $method_matched = false;
    foreach ($action_ref->getAttributes() as $attribute) {
      $attr_name = $attribute->getName();
      // 判断是否为方法注解
      if (in_array($attr_name, $this->attrs + config('plugin.wegar.validate.app.methods', []))) {
        $instance = $attribute->newInstance();
        if (!$instance instanceof MethodHelper) {
          continue;
        }
        // 如果方法注解没有设置name，则跳过
        if (!$instance->name) {
          continue;
        }
        // 判断是否匹配到请求方法
        if (strtoupper($instance->name) === strtoupper($request->method())) {
          $method_matched = true;
          // 执行方法注解的validate方法
          $instance->validate($action_ref);
          continue;
        }
        // 因为匹配到了设置的请求方法，则强制验证
        $method_force = true;
      }
    }
    // 如果方法注解没有匹配到，则判断是否强制验证
    return $method_matched || !$method_force;
  }
}
