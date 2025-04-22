<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class In extends RuleAbstract
{

  public static function getName(): string
  {
    return 'in';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $args = explode(',', $arg);
    if ($value !== null && !in_array($value, $args)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be in the list of %enum%', [
        '%field%' => $field,
        '%enum%'  => $arg
      ], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return "This rule is used to check if the value is in the given list.
example: in:1,2,3";
  }
}
