<?php

namespace Wegar\Validate\Helper;

class Valid
{
  static function validate($data, $rules=[])
  {
    foreach ($rules as $key => $rule) {
      if (!isset($data[$key])) {
        return false;
      }
      if (!preg_match($rule, $data[$key])) {
        return false;
      }
    }
  }
}