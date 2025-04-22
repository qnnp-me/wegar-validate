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
    if ($value !== null && !is_numeric($value)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be a float', ['%field%' => $field], 'wegar_validate'));
    }
    if ($value !== null && is_numeric($decimal)) {
      $decimal = (int)$decimal;
      $value_decimal = strlen(substr(strrchr($value, '.'), 1));
      if ($value_decimal != $decimal) {
        throw new InputValueException(trans($message ?: 'The %field% decimal must be %decimal%', [
          '%field%'   => $field,
          '%decimal%' => $decimal
        ], 'wegar_validate'));
      }
    }
  }

  public static function getDoc(): string
  {
    return "This rule is used to validate float value.
example: float:2 // means decimal must be 2
notice: if set decimal, ex: float:1, the value must be sent as string, like \"1.0\" or as float 1.1.";
  }
}
