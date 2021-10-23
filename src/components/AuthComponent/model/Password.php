<?php

namespace AuthComponent\Model;

use AuthComponent\PasswordIsEmptyException as PasswordIsEmptyException;
use AuthComponent\PasswordIsToShortException as PasswordIsToShortException;
use AuthComponent\PasswordNotMatchException as PasswordNotMatchException;

class Password {
  private static int $passwordMinNrOfCharacters = 6;
  private static int $minNrOfPasswords = 1;
  private static int $maxNrOfPasswords = 2;
  public string $password;

	/**
	* Instiates a password data strcutre that will check the inputs before creating and storing the password.
  * @param array $passwordInputs - Needs to be an array with passwords or password. Even if only one password is sent it will need to be sent in an array. 
  * Must be the same password in the array.
  * @param bool $isLoginPassword - Provide if this is a login password or register password and perform different controls depending if this is true or not.
	* @throws Exception If the passwords provided in array is not between min and maximum value it will throw an exception.
  * @throws PasswordIsEmptyException If the password is missing
  * @throws PasswordIsToShortException if the password is shorter than the minimum nr of characters.
  * @throws PasswordNotMatchException if the password inputs is not the same.
	*/
  public function __construct (array $passwordInputs, bool $isLoginPassword) {
    if (count($passwordInputs) < self::$minNrOfPasswords && count($passwordInputs) > self::$maxNrOfPasswords) {
      throw new \Exception('Internal error. Wrong number of password sent in array to verify password input. 
        Must be minimum ' . self::$minNrOfPasswords . ' and maximum ' . self::$maxNrOfPasswords . '
      ');
    }

    $this->password = $passwordInputs[0];

    if ($isLoginPassword) {
      if (mb_strlen($this->password) <= 0) {
        throw new PasswordIsEmptyException('Password is missing in form input');
      }

    } else {
      if (mb_strlen($this->password) < self::$passwordMinNrOfCharacters) {
        throw new PasswordIsToShortException('Password has to few characters in form input');
      }

      if (count($passwordInputs) > self::$minNrOfPasswords && $passwordInputs[0] !== $passwordInputs[1]) {
        throw new PasswordNotMatchException('The password and password repeat does not match');
      }
    }
  }
}

?>