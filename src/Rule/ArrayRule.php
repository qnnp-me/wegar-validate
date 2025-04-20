<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class ArrayRule extends RuleAbstract
{

  public static function getName(): string
  {
    return 'array';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !is_array($value)) {
      $message = $message ?: 'The field must be an array.';
      throw new InputValueException($message);
    }
  }
}