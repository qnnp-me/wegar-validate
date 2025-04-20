<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Not extends RuleAbstract
{

  public static function getName(): string
  {
    return 'not';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $args = explode(',', $arg);
    if (in_array($value, $args)) {
      throw new InputValueException($message ?: 'The value of ' . $field . ' cannot be ' . implode(' or ', $args));
    }
  }
  public static function getDoc(): string
  {
    return 'This rule is used to check if the value of the field is not in the list of values.';
  }
}