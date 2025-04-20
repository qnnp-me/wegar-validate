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
      throw new InputValueException($message ?: "{$field} must be in {$arg}");
    }
  }
}