<?php

namespace Wegar\MethodLimit\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class CONNECT
{
  public string $name = 'CONNECT';
}