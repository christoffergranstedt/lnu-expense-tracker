<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\ContainsIllegalCharException as ContainsIllegalCharException;

class User {
  private string $username;

	/**
	* Instiates a User with username.
  * @param string $username - A string with the username. 
  * @throws ContainsIllegalCharException If the password contains special characters an exception will be thrown.
	*/
  public function __construct (string $username) {
    $hasSpecialChars = preg_match('/[^a-zA-Z\d]/', $username);
    if ($hasSpecialChars) {
      throw new ContainsIllegalCharException("String for description contains special characters that are not allowed");
    }

    $this->username = $username;
  }

  public function getUsername () : string {
    return $this->username;
  }
}

?>