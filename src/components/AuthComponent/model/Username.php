<?php

namespace AuthComponent\Model;

use AuthComponent\UsernameIsEmptyException as UsernameIsEmptyException;
use AuthComponent\UsernameIsToShortException as UsernameIsToShortException;
use AuthComponent\UsernameContainsIllegalCharException as UsernameContainsIllegalCharException;

class Username {
  private static int $usernameMinNrOfCharacters = 3;
  public string $username;

	/**
	* Instiates a username data structure.
  * @param array $usernameInput - A string with username
  * @param bool $isLoginUsername - Provide if this is a login username or register username and perform different controls depending if this is true or not.
  * @throws UsernameIsEmptyException If the username is missing
  * @throws UsernameIsToShortException if the username is to short.
  * @throws UsernameContainsIllegalCharException If it containes special characters.
	*/
  public function __construct (string $usernameInput, bool $isLoginUsername) {
    if ($isLoginUsername) {
      if (mb_strlen($usernameInput) <= 0) {
        throw new UsernameIsEmptyException('Username is empty');
      }

    } else {
      if (mb_strlen($usernameInput) < self::$usernameMinNrOfCharacters) {
        throw new UsernameIsToShortException("Username is to short");
      }
      
      $hasSpecialChars = preg_match('/[^a-zA-Z\d]/', $usernameInput);
      if ($hasSpecialChars) {
        throw new UsernameContainsIllegalCharException("String contains special characters that are not allowed");
      }
    }

    $this->username = $usernameInput;
  } 
}

?>