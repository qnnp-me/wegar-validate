<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class ObjectRule extends RuleAbstract
{

  public static function getName(): string
  {
    return 'object';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && (!is_array($value) || array_is_list($value))) {
      throw new InputValueException(trans($message ?: 'The %field% field must be an object', [
        '%field%' => $field
      ], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return "This rule checks if the field is an object, meaning an associative array.";
  }
}