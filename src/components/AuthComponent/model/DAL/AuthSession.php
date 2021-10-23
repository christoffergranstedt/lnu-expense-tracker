<?php

namespace AuthComponent\Model\DAL;

use AuthComponent\LoginWrongUserAgentInSessionException as LoginWrongUserAgentInSessionException;
use AuthComponent\UserIsNotAuthorizedException as UserIsNotAuthorizedException;

class AuthSession {
	private static string $isLoggedInId = 'AuthSession::isLoggedInId';
	private static string $usernameId = 'AuthSession::usernameId';
	private static string $userAgentId = 'HTTP_USER_AGENT';
	private static string $cliName = 'cli';
	private static string $PHPVersion = '5.4.0';

	/**
	* Constructor for the class, that need to have a session started to run.
	* @throws Exception if there are no session started it will throw an error since the application is dependent on this to properly run.
	*/
  public function __construct() {
    if (!$this->isSessionStarted()) {
			throw new \Exception('Session is not started yet');
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

	public function regenerateId () : void {
    session_regenerate_id(true);
  }

	public function setLoggedInSession () : void {
		$_SESSION[self::$isLoggedInId] = true;
	}

	public function setUsernameInSession (string $username) : void {
		$_SESSION[self::$usernameId] = $username;
	}

	/**
	* Call this to get the the username that is stored in the session.
	* @throws UserIsNotAuthorizedException if this method is called if there is no session id with private usernameId as the id.
	*/
	public function getUsernameInSession () : string {
		if (!isset($_SESSION[self::$usernameId])) {
			throw new UserIsNotAuthorizedException('User is not logged in yet');
		} else {
			return $_SESSION[self::$usernameId];
		}
	}

	public function setLoggedOutSession () : void {
		unset($_SESSION[self::$isLoggedInId]);
	}

	public function setUserAgentForSession () : void {
		$_SESSION[self::$userAgentId] = $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	* Check if there is a logged in id in the session to know if the user is autenticated or not. Will also check if the users current user agent
	* is the same as stored in the session. This is due to check if there is a possible session hijack.
	* @throws LoginWrongUserAgentInSessionException if the current user agent is not the same as stored in the browser.
	* @return bool if this the user is logged in or not
	*/
	public function hasLoggedInSession () : bool {
		$isLoggedInIsSetInSession = isset($_SESSION[self::$isLoggedInId]);
		return ($isLoggedInIsSetInSession && $this->hasSameUserAgentAsSession());
	}

	private function hasSameUserAgentAsSession () : bool {
		if (!isset($_SESSION[self::$userAgentId])) {
			throw new LoginWrongUserAgentInSessionException('There is no session user agent set in this session.');
		}

		$sessionUserAgent = $_SESSION[self::$userAgentId];
		$currentUserAgent = $_SERVER[self::$userAgentId];
		if ($sessionUserAgent != $currentUserAgent) {
			throw new LoginWrongUserAgentInSessionException('The current user agent is not the same as stored in session.');
		}

		return true;
	}
}

?>