<?php

namespace Wegar\Validate\Rule;

use support\exception\InputValueException;
use Wegar\Validate\Abstract\RuleAbstract;

class Idcard extends RuleAbstract
{

  public static function getName(): string
  {
    return 'idcard';
  }

  public static function validate(string $field, mixed $value, ?string $arg = null, ?string $message = null): void
  {
    if ($value !== null) {
      $validated = false;
      $regex = "#^([1-9][0-9]{5})((19|20|21)[0-9]{2})(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[0-1])([0-9]{3}[0-9xX])$#";
      if (preg_match($regex, $value)) {
        $idcard = strtoupper($value);
        $validate_code = $idcard[17];
        $validate_code = $validate_code === 'X' ? 10 : $validate_code;
        $wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
          $sum += (int)($idcard[$i] === 'X' ? 10 : $idcard[$i]) * $wi[$i];
        }
        $check_code = ["1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2"][$sum % 11];
        $validated = $check_code === $validate_code;
      }
      if (!$validated) {
        throw new InputValueException(trans($message ?: 'The %field% field must be a valid ID card number', ['%field%' => $field], 'wegar_validate'));
      }
    }
  }

  public static function getDoc(): string
  {
    return 'The rule is to validate whether the Chinese ID card number is valid.';
  }
}
