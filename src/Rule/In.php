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
  public static function getDoc(): string
  {
    return <<<MD
This rule is used to check if the value is in the given list.

example: in:1,2,3
MD;
  }
}