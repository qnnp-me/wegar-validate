<?php

namespace Wegar\Validate\Rule;

use Wegar\Validate\Abstract\RuleAbstract;

class Integer extends IntRule
{

  public static function getName(): string
  {
    return 'integer';
  }
}