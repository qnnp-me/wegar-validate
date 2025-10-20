<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Regex extends RuleAbstract
{

  public static function getName(): string
  {
    return 'regex';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !preg_match("#$arg#", $value)) {
      throw new InputValueException(trans($message ?: 'The %field% field does not match the required format: %pattern%', [
        '%field%'   => $field,
        '%pattern%' => $arg,
      ], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return 'This rule validates that the field matches the given regular expression.';
  }
}
