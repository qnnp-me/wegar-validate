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
    if ($value !== null && !preg_match($arg, $value)) {
      $message = $message ?: 'The ' . $field . ' field does not match the required format';
      throw new InputValueException($message);
    }
  }
}