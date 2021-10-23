<?php

namespace AuthComponent\Controller;

use AuthComponent\Model\DAL\UsersDB as UsersDB;
use AuthComponent\Model\Username as Username;
use AuthComponent\Model\Password as Password;
use AuthComponent\Model\UserCredentials as UserCredentials;
use AuthComponent\View\ViewSession as ViewSession;
use AuthComponent\View\Register as RegisterView;
use AuthComponent\View\Layout as LayoutView;
use AuthComponent\PasswordNotMatchException as PasswordNotMatchException;
use AuthComponent\PasswordIsToShortException as PasswordIsToShortException;
use AuthComponent\UsernameIsToShortException as UsernameIsToShortException;
use AuthComponent\UsernameContainsIllegalCharException as UsernameContainsIllegalCharException;
use AuthComponent\RegisterUserExistException as RegisterUserExistException;

class Register {
  private UsersDB $usersDB;
  private ViewSession $viewSession;
  private RegisterView $registerView;
  private LayoutView $layoutView;

  public function __construct (UsersDB $usersDB, ViewSession $viewSession, LayoutView $layoutView) {
    $this->usersDB = $usersDB;
    $this->viewSession = $viewSession;
    $this->registerView = new RegisterView($this->viewSession);
    $this->layoutView = $layoutView;
  }

	/**
  * Call this to display the register form.
	* Updates the layoutview so it know will displaying a register form and the render the page a clear eventual messages set in session
	*/
  public function displayRegisterForm () : void {
    $this->layoutView->setIsRegistering(true);
    $this->layoutView->render($this->registerView);
    $this->viewSession->clearMessage();
  }

	/**
  * Call this to process a register request.
	* Will create usercredentails based of the registered values in the form and perform type checks to that input and throw Exceptions 
  * if it is not correct that will be catched by this method and then render different messages depending on the error. If an exception occurs
  * it will be displaying the message and thereafter exit execution and wait for user input again.
	*/
  public function processRegisterRequest () : void {
    try {
      $userCredentials = $this->getUserCredentials();
      $this->usersDB->addNewUser($userCredentials);
      $this->viewSession->setMessageToShow(ViewSession::messageRegisterSuccessful);

    } catch (PasswordNotMatchException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messagePasswordNotMatch); 
      $this->displayErrorMessageAndExit();

    } catch (PasswordIsToShortException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messagePasswordIsToShort); 
      $this->displayErrorMessageAndExit();

    } catch (UsernameIsToShortException $e) {
      $this->checkIfAlsoPasswordToShort();
      $this->displayErrorMessageAndExit();

    } catch (UsernameContainsIllegalCharException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameContainsIllegalChar); 
      $this->displayErrorMessageAndExit();

    } catch (RegisterUserExistException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageRegisterUserExist); 
      $this->displayErrorMessageAndExit();
    }
  }

  public function isRequestingRegister () : bool {
    return $this->registerView->isRequestingRegister();
  }

  private function displayErrorMessageAndExit () : void {
    $this->displayRegisterForm();
    $this->viewSession->clearMessage();
    exit();   
  }

	/**
  * Create and returns a UserCredentials object 
	* Will create usercredentails based of the registered values in the form and perform type checks to that input and throw Exceptions 
  * @throws PasswordNotMatchException if the password not match
  * @throws PasswordIsToShortException if the password is to short.
  * @throws UsernameIsToShortException if the username is to short.
  * @throws UsernameContainsIllegalCharException if the username contains illegals characters.
	*/
  private function getUserCredentials () : UserCredentials {
    $username = new Username($this->registerView->getRequestUsername(), false);

    $passwordInputs = [$this->registerView->getRequestPassword(), $this->registerView->getRequestPasswordRepeat()];
    $password = new Password($passwordInputs, false);

    return new UserCredentials($username, $password);
  }

  private function checkIfAlsoPasswordToShort () : void {
    try {
      new Password([$this->registerView->getRequestPassword()], false);
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameIsToShort); 
    } catch (PasswordIsToShortException $e) {
      $this->viewSession->setMessageToShow(ViewSession::messageUsernameAndPasswordIsToShort); 
    }
  }
}

?>