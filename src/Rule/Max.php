<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Max extends RuleAbstract
{

  public static function getName(): string
  {
    return 'max';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if (is_numeric($value)) {
      if ($value > $arg)
        throw new InputValueException($message ?: "{$field} must be less than or equal to {$arg}");
    } elseif (is_array($value)) {
      if (count($value) > $arg)
        throw new InputValueException($message ?: "{$field} count must be less than or equal to {$arg}");
    } elseif (strlen($value) > $arg) {
      throw new InputValueException($message ?: "{$field} length must be less than or equal to {$arg}");
    }
  }
}