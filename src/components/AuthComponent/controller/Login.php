<?php

namespace AuthComponent\Controller;

require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/View.php');

use AuthComponent\Model\DAL\AuthSession as AuthSession;
use AuthComponent\Model\DAL\UsersDB as UsersDB;
use AuthComponent\Model\Username as Username;
use AuthComponent\Model\Password as Password;
use AuthComponent\Model\UserCredentials as UserCredentials;
use AuthComponent\View\ViewSession as ViewSession;
use AuthComponent\View\Login as LoginView;
use AuthComponent\View\Layout as LayoutView;
use AuthComponent\PasswordNotMatchException as PasswordNotMatchException;
use AuthComponent\UsernameIsEmptyException as UsernameIsEmptyException;
use AuthComponent\PasswordIsEmptyException as PasswordIsEmptyException;
use AuthComponent\PasswordIsToShortException as PasswordIsToShortException;
use AuthComponent\UsernameIsToShortException as UsernameIsToShortException;
use AuthComponent\UsernameContainsIllegalCharException as UsernameContainsIllegalCharException;
use AuthComponent\LoginFailException as LoginFailException;
use AuthComponent\LoginWrongCookieException as LoginWrongCookieException;
use AuthComponent\LoginWrongUserAgentInSessionException as LoginWrongUserAgentInSessionException;

class Login {
  private static int $nrOfCharPasswordCookie = 72;
  private AuthSession $authSession;
  private UsersDB $usersDB;
  private ViewSession $viewSession;
  private LoginView $loginView;
  private LayoutView $layoutView;

  public function __construct (AuthSession $authSession, UsersDB $usersDB, ViewSession $viewSession, LayoutView $layoutView) {
    $this->authSession = $authSession;
    $this->usersDB = $usersDB;
    $this->viewSession = $viewSession;
    $this->loginView = new LoginView($this->viewSession);
    $this->layoutView = $layoutView;
  }

	/**
  * Call this to display the login form.
	* Renders the login form and clear eventual messages set in session
	*/
  public function displayLoginForm () : void {
    $this->layoutView->render($this->loginView);
    $this->viewSession->clearMessage();
  }

	/**
  * Call this if user is authenticated and wants to display logged in and a logout button.
	* Sets both the layoutview and loginview to know they will render isLoggedIn state therafter render the view and clear eventual message in session.
	*/
  public function displayAuthenticated () : void {
    $this->layoutView->setIsLoggedIn(true);
    $this->loginView->setIsLoggedIn(true);
    $this->layoutView->render($this->loginView);
    $this->viewSession->clearMessage();
  }

	/**
  * Call this if user has cookies in the browser to check if the user can be authenticated with that.
  * If it can it will store log in session and set a message to be shown on next display call.
	*/
  public function processLoginWithCookieRequest () : void {
    $this->authenticateStoredCookies();
    $this->setIsLoggedInSession();
    $this->viewSession->setMessageToShow(ViewSession::messageLoginWithCookiesSuccessful);
  }

	/**
  * Call this to process a login request.
	* Will create usercredentails based of the registered values in the form and perform type checks to that input and throw Exceptions 
  * if it is not correct that will be catched by this method and then render different messages depending on the error. If an exception occurs
  * it will be displaying the message and thereafter exit execution and wait for user input again. Will also store username a and a password in
  * cookies if user selected it want to rembember him.
	*/
  public function processLoginRequest () : void {
    try {
      $userCredentials = $this->getUserCredentials();
      $this->usersDB->authenticateByUsernameAndPassword($userCredentials->getUsername(), $userCredentials->getPassword());
      $this->setIsLoggedInSession();
      $this->authSession->setUsernameInSession($userCredentials->getUsername());

      if ($userCredentials->hasKeepLoggedInChecked()) {
        $this->setAndSaveCookies($userCredentials->getUsername());
        $this->viewSession->setMessageToShow(ViewSession::messageLoginKeepSuccessful);
      } else {
        $this->viewSession->setMessageToShow(ViewSession::messageLoginSuccessful);
      }

    } catch (PasswordNotMatchException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messagePasswordNotMatch); 
      $this->displayErrorMessageAndExit();

    } catch (UsernameIsEmptyException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameIsEmpty); 
      $this->displayErrorMessageAndExit();

    } catch (PasswordIsEmptyException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messagePasswordIsEmpty); 
      $this->displayErrorMessageAndExit();

    } catch (PasswordIsToShortException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messagePasswordIsEmpty); 
      $this->displayErrorMessageAndExit();

    } catch (UsernameIsToShortException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameIsToShort); 
      $this->displayErrorMessageAndExit();

    } catch (UsernameContainsIllegalCharException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameContainsIllegalChar);
      $this->displayErrorMessageAndExit();

    } catch (LoginFailException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageLoginFail);
      $this->displayErrorMessageAndExit();
    }
  }

	/**
  * Call this to process a ogout request.
	* Will remove cookies and sessions values in browser and also update the password cookie and expiration date that is set in the database.
	*/
  public function processLogoutRequest () : void {
    $this->loginView->removeLoginCookies();
    $this->authSession->setLoggedOutSession();
    $this->authSession->regenerateId();
    $this->layoutView->setIsLoggedIn(false);
    $this->loginView->setIsLoggedIn(false);
    $this->usersDB->updatePasswordCookieInfo('', '', 0);
    $this->viewSession->setMessageToShow(ViewSession::messageLogoutSuccessful);
  }

  public function isRequestingLogin () : bool {
    return $this->loginView->isRequestingLogin();
  }

  public function isRequestingLogout () : bool {
		return $this->loginView->isRequestingLogout();
	}

  public function hasCookiesWithLoginCredentials () : bool {
    return $this->loginView->hasCookiesWithLoginCredentials();
  }

  private function authenticateStoredCookies () : void {
    try {
      $usernameCookie = $this->loginView->getCookieName();
      $passwordCookie = $this->loginView->getCookiePassword();
      $this->usersDB->authenticateByCookies($usernameCookie, $passwordCookie);
      $this->authSession->setUsernameInSession($usernameCookie);
    } catch (LoginWrongCookieException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageLoginWithCookiesFail);
      $this->loginView->removeLoginCookies();
      $this->displayErrorMessageAndExit();
    } catch (LoginWrongUserAgentInSessionException $e) {
      $this->loginView->removeLoginCookies();
      $this->displayErrorMessageAndExit();
    }
  }

  private function displayErrorMessageAndExit () : void {
      $this->displayLoginForm();
      $this->viewSession->clearMessage();
      exit();   
  }

  private function getUserCredentials () : \AuthComponent\Model\UserCredentials {
    $username = new Username($this->loginView->getRequestUsername(), true);
    $password = new Password([$this->loginView->getRequestPassword()], true);
    $hasKeepLoggedInChecked = $this->loginView->isSessionKeepRequested();
    return new UserCredentials($username, $password, $hasKeepLoggedInChecked);
  }

  private function setIsLoggedInSession () : void {
    $this->authSession->regenerateId();
    $this->authSession->setLoggedInSession();
    $this->authSession->setUserAgentForSession();
  }

  private function setAndSaveCookies (string $usernameInput) : void {
      $passwordCookie = bin2hex(random_bytes(self::$nrOfCharPasswordCookie));
      $passwordCookieExpireDate = strtotime( '30days');
      $this->loginView->setLoginCookies($usernameInput, $passwordCookie, $passwordCookieExpireDate);
      $this->usersDB->updatePasswordCookieInfo($usernameInput, $passwordCookie, $passwordCookieExpireDate);
  }
}

?>