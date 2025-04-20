<?php

namespace Wegar\Validate\Rule;

class TestRule extends Regex
{

  public static function getName(): string
  {
    return 'test';
  }
}