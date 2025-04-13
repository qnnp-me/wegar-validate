<?php

namespace Wegar\MethodLimit\Middleware;

use ReflectionClass;
use ReflectionMethod;
use support\exception\BusinessException;
use support\exception\PageNotFoundException;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use Wegar\MethodLimit\Annotation\Method\CONNECT;
use Wegar\MethodLimit\Annotation\Method\DELETE;
use Wegar\MethodLimit\Annotation\Method\GET;
use Wegar\MethodLimit\Annotation\Method\HEAD;
use Wegar\MethodLimit\Annotation\Method\OPTIONS;
use Wegar\MethodLimit\Annotation\Method\PATCH;
use Wegar\MethodLimit\Annotation\Method\POST;
use Wegar\MethodLimit\Annotation\Method\PUT;
use Wegar\MethodLimit\Annotation\Method\TRACE;

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
        $use_notfound = !config('plugin.wegar.method-limit.app.throw', false);

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
    $attrs = $action_ref->getAttributes();
    $methods_marked = [];
    foreach ($attrs as $attr) {
      if (in_array($attr->getName(), $this->attrs + config('plugin.wegar.method-limit.app.methods', []))) {
        $methods_marked[] = $attr->newInstance()->name;
      }
    }
    $force = config('plugin.wegar.method-limit.app.force', true) || count($methods_marked) > 0;
    if (in_array(request()->method(), $methods_marked)) {
      return true;
    }
    if ($force) {
      return false;
    }
    return true;
  }
}