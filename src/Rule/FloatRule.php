<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class FloatRule extends RuleAbstract
{

  public static function getName(): string
  {
    return 'float';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $decimal = $arg;
    if ($value !== null && !is_float($value)) {
      throw new InputValueException($message ?: "{$field} must be float");
    }
    if ($value !== null && is_numeric($decimal)) {
      $decimal = (int)$decimal;
      $value_decimal = strlen(substr(strrchr($value, '.'), 1));
      if ($value_decimal != $decimal) {
        throw new InputValueException($message ?: "{$field} decimal must be {$decimal}");
      }
    }
  }
}