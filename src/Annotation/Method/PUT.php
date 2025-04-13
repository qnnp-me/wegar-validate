<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class PUT
{
  public string $name = 'PUT';
}