<?php
return [
  'enable'  => true,
  // 强制所有经过中间件的请求方法必须标注请求方式
  'force'   => true,
  // 抛出异常还是返回404
  'throw'   => false,
  // 自定义输入校验规则, 注意同名的规则会被覆盖, 可以是 [ 'rule_name' => 'rule_callable' ], 也可以是存放规则类文件的目录 ['rules_dir'], 可以混合设置
  'rules'   => [],
  // 自定义请求方式注解类，应该是继承了 \Wegar\Validate\Helper\MethodHelper 的类
  'methods' => [],
];