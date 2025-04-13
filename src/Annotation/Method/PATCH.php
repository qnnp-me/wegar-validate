<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PATCH
{
  public string $name = 'PATCH';
}