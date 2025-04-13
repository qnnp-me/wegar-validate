<?php

namespace Wegar\Validate\Middleware;

use ReflectionClass;
use ReflectionMethod;
use support\exception\BusinessException;
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

class HttpMethodLimiterMiddleware implements MiddlewareInterface
{
  private array $attrs = [
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
    $controller = $request->controller;
    if ($controller && class_exists($controller)) {
      $method = $request->action;
      $controller_ref = new ReflectionClass($controller);
      if ($controller_ref->hasMethod($method)) {

        $method_ref = $controller_ref->getMethod($method);
        $use_notfound = !config('plugin.wegar.validate.app.throw', false);

        if (!$this->checkMethod($method_ref)) {
          if ($use_notfound) {
            throw new PageNotFoundException();
          }
          throw new BusinessException('Method not allowed', 405);
        }
      }
    }
    return $handler($request);
  }

  private function checkMethod(ReflectionMethod $action_ref): bool
  {
    $force = config('plugin.wegar.validate.app.force', true);
    foreach ($action_ref->getAttributes() as $attribute) {
      if (in_array($attribute->getName(), $this->attrs + config('plugin.wegar.validate.app.methods', []))) {
        $attr_instance = $attribute->newInstance();
        if (property_exists($attr_instance, 'name') && request()->method() === $attr_instance->name) {
          return true;
        }
        $force = true;
      }
    }
    return !$force;
  }
}