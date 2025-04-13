<?php

namespace Wegar\MethodLimit\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HEAD
{
  public string $name = 'HEAD';
}