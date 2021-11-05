<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\InvalidDateException as InvalidDateException;

class Date {
  public string $date;

	/**
	* Instiates a Date with a string for date.
  * @param string $date - A string with the date. 
  * @throws InvalidDateException If the date is incorect.
	*/
  public function __construct (string $date) {
    if ($this->isValidDate($date)) {
      $this->date = $date;
    } else {
      throw new InvalidDateException("The date format is invalid. Shoud be 'YYYY-MM-DD'");
    }
  }

  private function isValidDate(string $date) : bool { 
    $format = 'Y-m-d';
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
}

?>