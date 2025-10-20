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
    if (!is_int((int)$value)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be an integer', ['%field%' => $field], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return 'This rule checks if the value is an integer';
  }
}
