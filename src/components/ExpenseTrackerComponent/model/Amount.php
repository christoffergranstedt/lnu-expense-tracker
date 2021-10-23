<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\NotANumberException as NotANumberException;
use ExpenseTrackerComponent\WrongValueException;

class Amount {
  private static int $minAmount = 0;
  public float $amount;

	/**
	* Instiates a Amount data structure.
  * @param string $amount - A string with the amount that will be transformed to a float. 
  * @throws NotANumberException If the string provided not is possible to convert to a float.
	*/
  public function __construct (string $amount) {
    if (!is_numeric($amount)) {
      throw new NotANumberException('The provided amount is not a number, possible due to it is not filled in.');
    }

    $this->amount = (float)$amount;

    if ($this->amount < self::$minAmount) {
      throw new WrongValueException('The amount is below the minimum accepted value ' . self::$minAmount . '');
    }
  }
}

?>