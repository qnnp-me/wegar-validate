<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class POST
{
  public string $name = 'POST';
}