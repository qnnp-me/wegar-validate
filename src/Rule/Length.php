<?php

namespace Wegar\Validate\Rule;

class Length extends Max
{

  public static function getName(): string
  {
    return 'length';
  }
}