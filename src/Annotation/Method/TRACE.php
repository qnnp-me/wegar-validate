<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class TRACE
{
  public string $name = 'TRACE';
}