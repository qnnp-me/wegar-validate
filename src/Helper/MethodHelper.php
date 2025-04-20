<?php

namespace Wegar\Validate\Helper;

use ReflectionFunction;
use ReflectionMethod;
use Wegar\Validate\Abstract\RuleAbstract;

class MethodHelper
{
  public string $name = '';
  protected static array $rule_list = [];

  public function __construct(
    protected array $rules = []
  )
  {
    $this->loadRules();
  }

  function validate(ReflectionMethod|ReflectionFunction $action_ref): void
  {
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
    $this->validateData($input_data, $this->rules);
  }

  protected function validateData($data, $rules, $parents = ''): void
  {
    foreach ($rules as $field => $rule) {
      if (is_callable($rule)) {
        call_user_func_array($rules, [$field, $data[$field] ?? null, $data, $parents]);
      } elseif (is_string($rule)) {
        $filed_rules = explode('|', $rule);
        static::executeValidate($filed_rules, $field, $data[$field] ?? null, $parents);
      } elseif (is_array($rule) && count($rule)) {
        if (array_is_list($rule)) {
          if (is_string($rule[0] ?? ($rule[0] = 'array'))) {
            if (!str_contains($rule[0], 'array')) {
              $rule[0] .= '|array';
            }
            $filed_rules = explode('|', $rule[0]);
            static::executeValidate($filed_rules, $field, $data[$field] ?? null, $parents);
          }
          $item_rules = $rule[1] ?? $rule[0] ?? [];
          foreach ($data[$field] ?? [] as $key => $item) {
            static::validateData($item, $item_rules, "{$parents}{$field}[{$key}].");
          }
        } else {
          if (is_callable($rule[0] ?? null)) {
            call_user_func_array($rule[0], [$field, $data[$field] ?? null, $data, $parents]);
            unset($rule[0]);
          } elseif (is_string($rule[0] ?? ($rule[0] = 'object'))) {
            if (!str_contains($rule[0], 'object')) {
              $rule[0] .= '|object';
            }
            $obj_rules = explode(':', $rule[0]);
            static::executeValidate($obj_rules, $field, $data[$field] ?? null, $parents);
            unset($rule[0]);
          }
          static::validateData($data[$field] ?? [], $rule, "{$parents}{$field}.");
        }
      }
    }
  }

  protected static function executeValidate($rules, $field, $value, $parents = ''): void
  {
    foreach ($rules as $rule) {
      if (is_string($rule)) {
        $rule_name = str_contains($rule, ':') ? (strstr($rule, ':', true) ?: $rule) : $rule;
        $rule_msg = self::getMessage($rule);
        $rule_arg = str_contains($rule, ':') ? trim(strstr($rule, ':') ?: '', ':') ?: null : $rule;
        if (isset(static::$rule_list[$rule_name]) && is_callable(static::$rule_list[$rule_name])) {
          call_user_func_array(
            static::$rule_list[$rule_name],
            [$parents . $field, $value, $rule_arg, $rule_msg]
          );
        }
      }
    }
  }

  protected static function getMessage(string &$arg): ?string
  {
    $args = explode(':', $arg);
    foreach ($args as $item) {
      if (str_starts_with($item, 'msg->')) {
        $arg = preg_replace('#:?msg->[^:]*#', '', $arg);
        return str_replace('msg->', '', $item);
      }
    }
    return null;
  }

  protected function loadRules(): void
  {
    if (empty(static::$rule_list)) {
      static::$rule_list = [];
      $config_rules = [
          base_path('vendor/wegar/validate/src/Rule/')
        ] + config('plugin.wegar.validate.app.rules', []);
      foreach ($config_rules as $rule_name => $rule) {
        // 读取目录下的规则类文件
        if (is_int($rule_name) && is_dir($rule)) {
          $namespace = '';
          foreach ((scandir($rule) ?: []) as $rule_file) {
            if (str_ends_with($rule_file, '.php')) {
              // 扫描命名空间
              if (!$namespace) {
                $file_content = file_get_contents($rule . DIRECTORY_SEPARATOR . $rule_file);
                if (preg_match('/namespace\s+([^\s;]+)/', $file_content, $matches)) {
                  $namespace = $matches[1];
                }
              }
              if ($namespace) {
                $class = $namespace . '\\' . str_replace('.php', '', $rule_file);
                if (class_exists($class) && is_subclass_of($class, RuleAbstract::class)) {
                  static::$rule_list[$class::getName()] = [$class, 'validate'];
                }
              }
            }
          }
        } elseif (is_string($rule_name) && is_callable($rule)) {
          static::$rule_list[$rule_name] = $rule;
        }
      }
    }
  }
}