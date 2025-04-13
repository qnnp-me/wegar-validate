<?php

namespace Wegar\MethodLimit\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class DELETE
{
  public string $name = 'DELETE';
}