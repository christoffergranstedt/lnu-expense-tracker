<?php
namespace AuthComponent\View;

class Register implements View {
  private static string $register = 'RegisterView::Register';
	private static string $username = 'RegisterView::UserName';
	private static string $password = 'RegisterView::Password';
	private static string $repeatPassword = 'RegisterView::PasswordRepeat';
	private static string $messageId = 'RegisterView::Message';

	private \AuthComponent\View\ViewSession $viewSession;

	public function __construct (\AuthComponent\View\ViewSession $viewSession) {
		$this->viewSession = $viewSession;
	}

	/**
	* Returns a HTML string with a register form.
	* Always gets the messages stored in session that can either be empty or not and return this message in the HTML String toghther with the other generated HTML.
	*/
  public function getHTMLString() : string {
		$message = $this->viewSession->getMessageToShow();

		$HTMLstring = '';
		$HTMLstring .= $this->getGeneratedRegisterFormHTML($message);
		
		return $HTMLstring;
	}

  public function getRequestPassword () : string {
		return isset($_POST[self::$password]) ? $_POST[self::$password] : '';
	}

  public function getRequestPasswordRepeat () : string {
		return isset($_POST[self::$repeatPassword]) ? $_POST[self::$repeatPassword] : '';
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

	public function isRequestingRegister () : bool {
    return isset($_POST[self::$register]);
  }

	private function getGeneratedRegisterFormHTML (string $message) : string {
		return '
			<form action="?register" method="post" enctype="multipart/form-data"> 
				<fieldset>
					<legend>Register a new user - Write username and password</legend>
					<p class="message" id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$username . '">Username :</label>
					<input type="text" id="' . self::$username . '" name="' . self::$username . '" value="' . $this->viewSession->getEnteredUsername() . '" />
          <br/>

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
          <br/>

					<label for="' . self::$repeatPassword . '">Repeat password :</label>
					<input type="password" id="' . self::$repeatPassword . '" name="' . self::$repeatPassword . '" />
          <br/>

					<input id="submit" type="submit" name="' . self::$register . '" value="Register" />
          <br/>
				</fieldset>
			</form>
		';
	}
}

?>