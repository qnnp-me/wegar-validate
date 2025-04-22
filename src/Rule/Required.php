<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Required extends RuleAbstract
{

  public static function getName(): string
  {
    return 'required';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value === null || $value === '' || $value === []) {
      throw new InputValueException(trans($message ?: 'The %field% field is required', [
        '%field%' => $field
      ], 'wegar_validate'));
    }
  }
}
