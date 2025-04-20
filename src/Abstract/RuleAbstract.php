<?php

namespace Wegar\Validate\Abstract;

abstract class RuleAbstract
{
  abstract public static function getName(): string;

  abstract public static function validate(
    string  $field,
    mixed   $value,
    ?string $arg = null,
    ?string $message = null
  ): void;

}