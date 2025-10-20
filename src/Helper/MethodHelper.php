<?php

namespace Wegar\Validate\Helper;

use ReflectionFunction;
use ReflectionMethod;
use Wegar\Validate\Abstract\RuleAbstract;

class MethodHelper
{
  protected static array $rule_list = [];
  public string $name = '';

  public function __construct(
    protected array $rules = []
  )
  {
    $this->loadRules();
  }

  /**
   * 加载校验规则
   * @return void
   */
  protected function loadRules(): void
  {
    if (!empty(static::$rule_list)) {
      return;
    }

    static::$rule_list = [];
    $configRules = [
        base_path('vendor/wegar/validate/src/Rule/')
      ] + config('plugin.wegar.validate.app.rules', []);

    foreach ($configRules as $ruleName => $rule) {
      if (is_int($ruleName) && is_dir($rule)) {
        $this->loadDirectoryRules($rule);
      } elseif (is_string($ruleName) && is_callable($rule)) {
        static::$rule_list[$ruleName] = $rule;
      }
    }
  }

  /**
   * 加载指定目录中的所有规则类
   */
  protected function loadDirectoryRules(string $directory): void
  {
    $files = scandir($directory) ?: [];
    $namespace = null;

    foreach ($files as $file) {
      if (!str_ends_with($file, '.php')) {
        continue;
      }

      if ($namespace === null) {
        $namespace = $this->extractNamespaceFromFile($directory . DIRECTORY_SEPARATOR . $file);
      }

      if (!$namespace) {
        continue;
      }

      $className = $namespace . '\\' . str_replace('.php', '', $file);
      if (class_exists($className) && is_subclass_of($className, RuleAbstract::class)) {
        static::$rule_list[$className::getName()] = [$className, 'validate'];
      }
    }
  }

  /**
   * 从文件中提取命名空间
   */
  protected function extractNamespaceFromFile(string $filePath): ?string
  {
    $content = file_get_contents($filePath);
    if (preg_match('/namespace\s+([^\s;]+)/', $content, $matches)) {
      return '\\' . $matches[1];
    }

    return null;
  }

  /**
   * 请求方法的入参校验
   * @param ReflectionMethod|ReflectionFunction $action_ref
   * @return void
   */
  function validate(ReflectionMethod|ReflectionFunction $action_ref): void
  {
    if (empty($this->rules)) return;
    if ($this->name !== request()->method()) return;
    $is_post = in_array($this->name, ['POST', 'PUT', 'PATCH']);
    $is_get = in_array($this->name, ['GET', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT']);
    if ($is_post) {
      $input_data = request()->post() + request()->get();
    } else {
      $input_data = request()->all();
    }
    $input_data += request()->route?->param() ?? [];
    $this->validateData($input_data, $this->rules);
  }

  /**
   * 验证数据
   * @param array $data 数据
   * @param array $rules 规则
   * @param $parents
   * @return void
   */
  protected function validateData(array $data, array $rules, string $parents = ''): void
  {
    foreach ($rules as $field => $rule) {
      if (is_callable($rule) && $rule !== 'date') {
        call_user_func_array($rule, [$field, $data[$field] ?? null, $data, $parents]);
      } elseif (is_string($rule)) {
        $fieldRules = preg_split('/(?<!\\\)\|/', $rule);
        static::executeValidate($fieldRules, $field, $data[$field] ?? null, $parents);
      } elseif (is_array($rule) && count($rule)) {
        if (array_is_list($rule)) {
          $this->handleListRule($rule, $field, $data, $parents);
        } else {
          $this->handleAssocRule($rule, $field, $data, $parents);
        }
      }
    }
  }

  protected function handleListRule(array &$rule, string $field, array $data, string $parents): void
  {
    if (is_string($rule[0] ?? ($rule[0] = 'array'))) {
      if (!str_contains($rule[0], 'array')) {
        $rule[0] .= '|array';
      }
      $fieldRules = preg_split('/(?<!\\\)\|/', $rule[0]);
      static::executeValidate($fieldRules, $field, $data[$field] ?? null, $parents);
    }

    $itemRules = $rule[1] ?? $rule[0] ?? [];
    foreach ($data[$field] ?? [] as $key => $item) {
      static::validateData($item, (array)$itemRules, "$parents$field[$key].");
    }
  }

  protected function handleAssocRule(array &$rule, string $field, array $data, string $parents): void
  {
    if (is_callable($rule[0] ?? null)) {
      call_user_func_array($rule[0], [$field, $data[$field] ?? null, $data, $parents]);
      unset($rule[0]);
    } elseif (is_string($rule[0] ?? ($rule[0] = 'object'))) {
      if (!str_contains($rule[0], 'object')) {
        $rule[0] .= '|object';
      }
      $objRules = preg_split('/(?<!\\\):/', $rule[0]);
      static::executeValidate($objRules, $field, $data[$field] ?? null, $parents);
      unset($rule[0]);
    }

    static::validateData($data[$field] ?? [], $rule, "$parents$field.");
  }
  protected static function executeValidate($rules, $field, $value, $parents = ''): void
  {
    foreach ($rules as $rule) {
      if (!is_string($rule)) continue;

      self::executeSingleRule($rule, $field, $value, $parents);
    }
  }

  protected static function executeSingleRule(string $rule, string $field, $value, string $parents = ''): void
  {
    $parsedRule = self::parseRule($rule);
    $ruleName   = $parsedRule['ruleName'];
    $ruleArg    = $parsedRule['ruleArg'];
    $ruleMsg    = $parsedRule['ruleMsg'];

    if (isset(static::$rule_list[$ruleName]) && is_callable(static::$rule_list[$ruleName])) {
      call_user_func_array(
        static::$rule_list[$ruleName],
        [$parents . $field, $value, $ruleArg, $ruleMsg]
      );
    }
  }

  /**
   * 解析单条规则字符串
   */
  protected static function parseRule(string $rule): array
  {
    $ruleName = str_contains($rule, ':') ? (strstr($rule, ':', true) ?: $rule) : $rule;
    $ruleMsg  = self::getMessage($rule);
    $ruleArg  = preg_replace('#^' . preg_quote($ruleName, '#') . ':?#', '', $rule) ?: null;

    return compact('ruleName', 'ruleArg', 'ruleMsg');
  }
  protected static function getMessage(string &$arg): ?string
  {
    if (str_contains($arg, 'msg->')) {
      $msg = strstr($arg, 'msg->');
      $arg = preg_replace('#:?msg->.*#', '', $arg);
      return str_replace('msg->', '', $msg);
    }
    return null;
  }
}