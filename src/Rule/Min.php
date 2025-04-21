<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Min extends RuleAbstract
{

  public static function getName(): string
  {
    return 'min';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $validated = true;
    if (is_numeric($value)) {
      if ($value < $arg)
        $validated = false;
    } elseif (is_array($value)) {
      if (count($value) < $arg)
        $validated = false;
    } elseif (strlen($value) < $arg) {
      $validated = false;
    }
    if (!$validated)
      throw new InputValueException(trans($message ?: 'The %field% length or value must be greater than or equal to %min%', [
        '%field%' => $field,
        '%min%'   => $arg
      ], 'wegar_validate'));
  }

  public static function getDoc(): string
  {
    return 'This rule is used to validate the minimum length of a string or array or the minimum value of a number.';
  }
}