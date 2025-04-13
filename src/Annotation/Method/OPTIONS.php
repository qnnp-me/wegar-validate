<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class OPTIONS
{
  public string $name = 'OPTIONS';
}