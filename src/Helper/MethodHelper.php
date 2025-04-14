<?php

namespace Wegar\Validate\Helper;

use Exception;
use ReflectionFunction;
use ReflectionMethod;

class MethodHelper
{
  protected static array $rule_list = [];

  public function __construct(
    protected array $rules = []
  )
  {
    if (empty(static::$rule_list)) {
      static::$rule_list = [] + config('plugin.wegar.validate.app.rules', []);
    }
  }

  function validate(ReflectionMethod|ReflectionFunction $action_ref): void
  {
    if (!isset($this->name)) throw new Exception('@' . get_class($this) . '->name is required');
    if (empty($this->rules)) return;
    if ($this->name !== request()->method()) return;
    $is_post = in_array($this->name, ['POST', 'PUT', 'PATCH']);
    $is_get = in_array($this->name, ['GET', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT']);
    if ($is_post) {
      $input_data = request()->post();
    } else if ($is_get) {
      $input_data = request()->get();
    } else {
      $input_data = request()->all();
    }
    foreach ($this->rules as $field => $rules) {
      $value = $input_data[$field] ?? null;
      if (is_string($rules)) {
        $rules = explode('|', $rules);
      }
      foreach ($rules as $rule) {
        $rule = explode(':', $rule);
        $rule_name = $rule[0];
        $rule_args = array_slice($rule, 1);
        if (isset(static::$rule_list[$rule_name]) && is_callable(static::$rule_list[$rule_name])) {
          call_user_func_array(static::$rule_list[$rule_name], [$value, $rule_args]);
        }
      }
    }
  }
}