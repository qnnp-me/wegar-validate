<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Ip extends RuleAbstract
{

  public static function getName(): string
  {
    return 'ip';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null && !filter_var($value, FILTER_VALIDATE_IP)) {
      throw new InputValueException($message ?: 'The ' . $field . ' field must be a valid IP address');
    }
  }

  public static function getDoc(): string
  {
    return <<<MD
This rule validates that the field is a valid IP address.

See [PHP Documentation](https://www.php.net/manual/en/filter.constants.php#constant.filter-validate-ip)
MD;
  }
}