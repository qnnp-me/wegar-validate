<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Email extends RuleAbstract
{

  public static function getName(): string
  {
    return 'email';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
      throw new InputValueException($message ?: "{$field} is not a valid email address");
    }
  }
}