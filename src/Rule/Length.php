<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;

class Length extends Max
{

  public static function getName(): string
  {
    return 'length';
  }
  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $validated = true;
    if (is_array($value)) {
      if (count($value) > $arg)
        $validated = false;
    } elseif (strlen($value) > $arg) {
      $validated = false;
    }
    if (!$validated)
      throw new InputValueException(trans($message ?: 'The %field% length must be less than or equal to %max%', [
        '%field%' => $field,
        '%max%'   => $arg
      ], 'wegar_validate'));
  }
}