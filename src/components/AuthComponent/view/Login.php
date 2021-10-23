<?php

namespace AuthComponent\View;

use AuthComponent\LoginWrongCookieException as LoginWrongCookieException;

class Login implements View {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $messageId = 'LoginView::Message';
	private static $username = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';

	private ViewSession $viewSession;
	private bool $isLoggedIn;

	public function __construct (ViewSession $viewSession) {
		$this->viewSession = $viewSession;
		$this->isLoggedIn = false;
	}

	/**
	* Returns a HTML string depding that can vary depending if the isLoggedIn is set to true or false and this can be set from a method in this class.
	* Always gets the messages stored in session that can either be empty or not and return this message in the HTML String togheter with the other generated HTML.
	* Will either return a login form if the user is not logged in or and authenticated and logout button if the user is authenticated.
	*/
	public function getHTMLString  () : string {
		$message = $this->viewSession->getMessageToShow();

		$HTMLString = '';
		if ($this->isLoggedIn) {
			$HTMLString = $this->getGeneratedLogoutButtonHTML($message);
		} else {
			$HTMLString = $this->getGeneratedLoginFormHTML($message);
		}

		return $HTMLString;
	}

	public function setIsLoggedIn (bool $isLoggedIn) : void {
    $this->isLoggedIn = $isLoggedIn;
  }
	
	public function getRequestUsername () : string {
		$usernameInput = $_POST[self::$username];
		if (isset($usernameInput)) {
			$this->viewSession->setEnteredUsername($usernameInput);
			return $usernameInput;
		} else {
			return '';
		}
	}

	public function getRequestPassword () : string {
		return isset($_POST[self::$password]) ? $_POST[self::$password] : '';
	}

	
	public function isSessionKeepRequested () : bool {
		return isset($_POST[self::$keep]);
	}


	public function isRequestingLogin () : bool {
    return isset($_POST[self::$login]);
  }

	public function isRequestingLogout () : bool {
		return isset($_POST[self::$logout]);
	}

	public function hasCookiesWithLoginCredentials () : bool {
    return (isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword]));
  }

	public function removeLoginCookies () : void {
		if (isset($_COOKIE[self::$cookiePassword])) {
			setcookie(self::$cookiePassword, null, -1);
		}
		if (isset($_COOKIE[self::$cookieName])) {
			setcookie(self::$cookieName, null, -1);
		}
	}

	public function setLoginCookies (string $username, $token, int $tokenExpireDate) : void {
		$environment = new \Environment();
		$isSecureCookie = ($environment->isProductionEnvironment()); // Secure cookie if https (in production)
		$cookieOptions = ['expires' => $tokenExpireDate, 'httponly'=> true, 'secure' => $isSecureCookie, 'samesite' => 'lax'];
		setcookie(self::$cookieName, $username, $cookieOptions);
		setcookie(self::$cookiePassword, $token, $cookieOptions);
	}

	public function getCookieName () : string {
		if (!isset($_COOKIE[self::$cookieName])) {
			throw new LoginWrongCookieException('The cookie name is not set.');
		}
		return $_COOKIE[self::$cookieName];
	}

	public function getCookiePassword () : string {
		if (!isset($_COOKIE[self::$cookiePassword])) {
			throw new LoginWrongCookieException('The cookie password is not set.');
		}
		return $_COOKIE[self::$cookiePassword];
	}

	private function getGeneratedLoginFormHTML (string $message) : string {
		return '
			<form method="post"> 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p class="message" id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$username . '">Username :</label>
					<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $this->viewSession->getEnteredUsername() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	private function getGeneratedLogoutButtonHTML (string $message) : string {
    return '
      <form  method="post" >
        <p class="message" id="' . self::$messageId . '">' . $message .'</p>
        <input id="logout" type="submit" name="' . self::$logout . '" value="logout"/>
      </form>
    ';
	}
}

?>