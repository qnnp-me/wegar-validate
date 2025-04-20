<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Required extends RuleAbstract
{

  public static function getName(): string
  {
    return 'required';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if (empty($value)) {
      throw new InputValueException($message ?: "{$field} is required");
    }
  }
}