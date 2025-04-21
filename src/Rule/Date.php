<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Date extends RuleAbstract
{

  public static function getName(): string
  {
    return 'date';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    $format = $arg ?: 'Y-m-d H:i:s';
    if ($value !== null && !date_create_from_format($format, $value)) {
      throw new InputValueException(trans($message ?: 'The %field% field must be a valid date format: %format%', [
        '%field%'  => $field,
        '%format%' => $format
      ], 'wegar_validate'));
    }
  }

  public static function getDoc(): string
  {
    return <<<MD
      This rule checks if the value is a valid date format.
      
      example: date:Y-m-d H:i:s
      MD;
  }
}