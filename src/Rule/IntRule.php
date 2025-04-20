<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class IntRule extends RuleAbstract
{

  public static function getName(): string
  {
    return 'int';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if (!(is_numeric($value) && !is_float(1 * $value) && is_int(1 * $value))) {
      $message = $message ?: 'The ' . $field . ' field must be an integer';
      throw new InputValueException($message);
    }
  }
}