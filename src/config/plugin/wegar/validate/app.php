<?php
return [
  'enable'  => true,
  'force'   => true, // 强制所有经过中间件的请求方法必须标注请求方式
  'throw'   => false, // 抛出异常还是返回404
  'rules'   => [], // 自定义校验规则
  'methods' => [], // 自定义请求方式注解类
];