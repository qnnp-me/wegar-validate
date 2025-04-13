<?php

use Wegar\MethodLimit\Middleware\HttpMethodLimiterMiddleware;

return [
  '' => [ // 如需超全局请手动设置，并谨慎设置！
    HttpMethodLimiterMiddleware::class
  ]
];