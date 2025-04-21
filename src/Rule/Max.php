<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Max extends RuleAbstract
{

  public static function getName(): string
  {
    return 'max';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $validated = true;
    if (is_numeric($value)) {
      if ($value > $arg)
        $validated = false;
    } elseif (is_array($value)) {
      if (count($value) > $arg)
        $validated = false;
    } elseif (strlen($value) > $arg) {
      $validated = false;
    }
    if (!$validated)
      throw new InputValueException(trans($message ?: 'The %field% length or value must be less than or equal to %max%', [
        '%field%' => $field,
        '%max%'   => $arg
      ], 'wegar_validate'));
  }

  public static function getDoc(): string
  {
    return 'This rule is used to validate the maximum length of a string or array or the maximum value of a number.';
  }
}