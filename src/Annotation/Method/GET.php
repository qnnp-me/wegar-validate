<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class GET
{
  public string $name = 'GET';
}