<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\InvalidNumberOfCharsException as InvalidNumberOfCharsException;
use \ExpenseTrackerComponent\ContainsIllegalCharException as ContainsIllegalCharException;

class Description {
  private static int $minNrOfCharacters = 1;
  private static int $maxNrOfCharacters = 200;
  public string $description;

	/**
	* Instiates a Description with a string for description.
  * @param string $description - A string with the description. 
  * @throws InvalidNumberOfCharsException If the descirption contains to many characters or to few.
  * @throws ContainsIllegalCharException If the descirption contains special characters.
	*/
  public function __construct (string $description) {
    if (mb_strlen($description) > self::$maxNrOfCharacters) {
      throw new  InvalidNumberOfCharsException('To many chars in description, maximum number of characters is ' . self::$maxNrOfCharacters . '');
    }

    if (mb_strlen($description) < self::$minNrOfCharacters) {
      throw new  InvalidNumberOfCharsException('To few characters in description, minimum number of characters is ' . self::$minNrOfCharacters . '');
    }

    $hasSpecialChars = preg_match('/[^a-zA-Z\d]/', $description);
    if ($hasSpecialChars) {
      throw new ContainsIllegalCharException("String for description contains special characters that are not allowed");
    }

    $this->description = $description;
  }
}

?>