<?php 

namespace AuthComponent\Controller;

use AuthComponent\Controller\Login as LoginController;
use AuthComponent\Controller\Register as RegisterController;
use AuthComponent\Model\DAL\AuthSession as AuthSession;
use AuthComponent\View\Layout as LayoutView;
use AuthComponent\View\ViewSession as ViewSession;
use AuthComponent\Model\DAL\UsersDB as UsersDB;
use AuthComponent\LoginWrongUserAgentInSessionException as LoginWrongUserAgentInSessionException;
use AuthComponent\UserIsNotAuthorizedException as UserIsNotAuthorizedException;

class Main {
  private \Settings $settings;
  private AuthSession $authSession;
  private UsersDB $usersDB;
  private ViewSession $viewSession;
  private LayoutView $layoutView;
  private LoginController $loginController;
  private RegisterController $registerController;

 /**
   * Constructor for the class
   *
   * Important to give a Settings object when creating this object. The settings object must create a PDO database connection,
   * and a getDBConnection() method to get the database connection. It must also contain a prefix which will be used to create
   * a seperate named table name and the prefix should be possible to call with a getPrefix method.
   * @param Settings $settings - a settings object must be provided. Must have getPrefix() and getDBConnection() methods.
   */
  public function __construct (\Settings $settings) {
    $this->settings = $settings;
    $this->authSession = new AuthSession();
    $this->viewSession = new ViewSession();
    $this->layoutView = new LayoutView();
    $this->setupDBConnectionAndTables();
    $this->setupControllers();
  }

	/**
	* The main method to run the component.
  * Will listen to user interactions when user is posting form or changing url and will change the state depending
  * on that and render the correct output.
	*/
  public function run () : void {
    try {
      $this->listenToUserInteractions();
      $this->displayPages();
    } catch (LoginWrongUserAgentInSessionException $e) {
      $this->loginController->displayLoginForm();
    }
  }

	/**
	* To check if the user is is logged in in session and return true or false.
	*/
  public function isUserAuthenticated () {
    try {
      return $this->authSession->hasLoggedInSession();
    } catch (LoginWrongUserAgentInSessionException $e) {
      $this->loginController->displayLoginForm();
      exit();
    }
  }

	/**
	* To get the string of the username of the authenticated user.
  * Please call only when the user is authenticated. See isUserAuthenticated() for ability to check that.
  * @throws UserIsNotAuthorizedException is thrown from this method if is called if not a user is authenticated.
	*/
  public function getAuthenticatedUser () : string {
    if (!$this->authSession->hasLoggedInSession()) {
      throw new UserIsNotAuthorizedException('User is not logged in yet');
    } else {
      return $this->authSession->getUsernameInSession();
    }
  }

	/**
	* A method to start to create a table to store the users in. Will use the Settings provided in AuthComponent
  * and call getDBConnection() to get a PDO connection that will be uses to instiate the UsersDB. Please also note
  * that a prefix must be included in the settings that will be used to create a separate table for your applications user. 
	*/
  private function setupDBConnectionAndTables () : void {
    $dbConnection = $this->settings->getDBConnection();
    $this->usersDB = new UsersDB($dbConnection, $this->settings->getPrefix());
  }

  private function setupControllers () : void {
    $this->loginController = new LoginController($this->authSession, $this->usersDB, $this->viewSession, $this->layoutView);
    $this->registerController = new RegisterController($this->usersDB, $this->viewSession, $this->layoutView); 
  }

	/**
	* A method that will listen to user interactions, and will check for different interactions depending 
  * on the user has a logged in session or not. This will include if the user is requesting to logout,
  * if the user is not logged in but has cookies in the browser for this site and attempt to login. 
  * Or if the user is requesting to login or register. And will then call other controllers method to process
  * that request.
	*/
  private function listenToUserInteractions () : void {
    if ($this->authSession->hasLoggedInSession()) {
      $this->listenToUserInteractionsWhileLoggedIn();
    } else {
      $this->listenToUserInteractionsWhileNotLoggedIn();
    }
  }

  private function listenToUserInteractionsWhileLoggedIn () : void {
    if ($this->loginController->isRequestingLogout()) {
      $this->loginController->processLogoutRequest();
      $this->redirectToIndex();
    }
  }

  private function listenToUserInteractionsWhileNotLoggedIn () : void {
    if ($this->loginController->hasCookiesWithLoginCredentials()) {
      $this->loginController->processLoginWithCookieRequest();   

    } elseif ($this->loginController->isRequestingLogin()) {
      $this->loginController->processLoginRequest();
      $this->redirectToIndex();

    } elseif ($this->registerController->isRequestingRegister()) {
      $this->registerController->processRegisterRequest();
      $this->redirectToIndex();
    }
  }

  /**
	* Will render the output based on the state changes that will have happened in listenToUserInteractions.
  * Will call different controllers depending if the user is logged in in Session, is requesting to view the
  * register or login form.
	*/
  private function displayPages () : void {
    if ($this->authSession->hasLoggedInSession()) {
      $this->loginController->displayAuthenticated();
    
    } elseif ($this->layoutView->isRequestingRegisterForm()) {
      $this->registerController->displayRegisterForm();

    } else {
      $this->loginController->displayLoginForm();
    }
  }

  /**
	* After posting have done it is possible to call this method to set user at the index page and possible to
  * reload and get the post request sent again.
	*/
  private function redirectToIndex () : void {
    exit(header('Location: /index.php', true, 302));
  }
}

?>