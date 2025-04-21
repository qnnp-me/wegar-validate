<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class BoolRule extends RuleAbstract
{

  public static function getName(): string
  {
    return 'bool';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !is_bool($value)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be a bool', ['%field%' => $field], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return 'This rule checks if the value is a bool.';
  }
}