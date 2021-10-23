<?php

namespace AuthComponent\View;

	/**
	* A class to handle session in view that is to storing messages and username.
  * Have a different number of messages that can be set with setMessageToShow() and calling the messages from the outside.
	*/
class ViewSession {
  private static string $messageId = 'ViewSession::messageId';
  private static string $cliName = 'cli';
	private static string $PHPVersion = '5.4.0';
  private static string $enteredUsernameId = 'EnteredUserNameId';
  const messageLogoutSuccessful = 'Bye bye!';
  const messageLoginKeepSuccessful = 'Welcome and you will be remembered';
  const messageLoginSuccessful = 'Welcome';
  const messageLoginFail = 'Wrong name or password';
  const messageLoginWithCookiesSuccessful = 'Welcome back with cookie';
  const messageRegisterSuccessful = 'Registered new user.';
  const messagePasswordNotMatch = 'Passwords do not match.';
  const messageUsernameIsEmpty = 'Username is missing';
  const messagePasswordIsEmpty = 'Password is missing';
  const messageUsernameAndPasswordIsToShort = 'Username has too few characters, at least 3 characters.' . '<br/>' .  'Password has too few characters, at least 6 characters.';
  const messagePasswordIsToShort = 'Password has too few characters, at least 6 characters.';
  const messageUsernameIsToShort = 'Username has too few characters, at least 3 characters.';
  const messageUsernameContainsIllegalChar = 'Username contains invalid characters.';
  const messageRegisterUserExist = 'User exists, pick another username.';
  const messageLoginWithCookiesFail = 'Wrong information in cookies';

	/**
	* Constructor for the class, that need to have a session started to run.
	* @throws Exception if there are no session started it will throw an error since the application is dependent on this to properly run.
	*/
  public function __construct() {
    if (!$this->isSessionStarted()) {
			throw new \Exception('Session is not started');
		}
  }

	//https://www.php.net/manual/en/function.session-status.php
	private function isSessionStarted () : bool {
    if ( php_sapi_name() !== self::$cliName ) {
      if ( version_compare(phpversion(), self::$PHPVersion, '>=') ) {
        return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
      } else {
        return session_id() === '' ? FALSE : TRUE;
      }
    }
    return FALSE;
	}

  public function getMessageToShow () : string {
    $message = isset($_SESSION[self::$messageId]) ? $_SESSION[self::$messageId] : '';
    return (string)$message;
  }

  public function setMessageToShow (string $message) : void {
    $_SESSION[self::$messageId] = $message;
  }

  public function clearMessage () : void {
    unset($_SESSION[self::$messageId]);
  }

  public function setEnteredUsername ($username) : void {
		$_SESSION[self::$enteredUsernameId] = $this->removeSpecialCharactersFromString($username);
	}

  public function getEnteredUsername () : string {
		return isset($_SESSION[self::$enteredUsernameId]) ? $_SESSION[self::$enteredUsernameId] : "";
	}

  private function removeSpecialCharactersFromString (string $value) : string {
		$strippedValue = strip_tags($value);
		return preg_replace('/[^a-zA-Z0-9_ -]/s','',$strippedValue);
	}
}

?>