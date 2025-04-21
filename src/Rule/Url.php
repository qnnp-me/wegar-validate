<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Url extends RuleAbstract
{

  public static function getName(): string
  {
    return 'url';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !filter_var($value, FILTER_VALIDATE_URL)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be a valid URL', [
        '%field%' => $field
      ], 'wegar_validate'));
    }
  }
}