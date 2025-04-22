<?php

use support\exception\PageNotFoundException;
use support\Request;
use Wegar\Validate\Annotation\Method\GET;
use Wegar\Validate\Annotation\Method\POST;
use Wegar\Validate\Middleware\WegarValidateMiddleware;

class MiddlewareTest extends \PHPUnit\Framework\TestCase
{
  function testMiddleware()
  {
    $request = new Request("GET / HTTP/1.1\r\nHost: localhost\r\n\r\n");
    $response_handler = #[GET] function (Request $request) {
      return response('hello');
    };
    $middleware = new WegarValidateMiddleware();
    $response = $middleware->process($request, $response_handler);
    $this->assertEquals("hello", $response->rawBody());
    $response_handler = #[POST] function (Request $request) {
      return response('hello');
    };
    $this->expectException(PageNotFoundException::class);
    $middleware->process($request, $response_handler);
  }
}
