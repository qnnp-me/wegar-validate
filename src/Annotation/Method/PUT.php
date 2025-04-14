<?php

namespace Wegar\Validate\Annotation\Method;

use Attribute;
use Wegar\Validate\Helper\MethodHelper;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class PUT extends MethodHelper
{
  public string $name = 'PUT';
}