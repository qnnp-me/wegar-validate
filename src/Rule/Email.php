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
      throw new InputValueException(trans($message ?: 'The %field% field must be a valid email address', ['%field%' => $field], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return 'This rule validates whether the value is a valid email address.';
  }
}