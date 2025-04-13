<?php

use Wegar\MethodLimit\Middleware\HttpMethodLimiterMiddleware;

return [
  '@' => [
    HttpMethodLimiterMiddleware::class
  ]
];