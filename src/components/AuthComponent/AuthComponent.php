<?php

namespace AuthComponent;

require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/controller/Main.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/controller/Login.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/controller/Register.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/model/DAL/UsersDB.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/model/DAL/AuthSession.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/model/Username.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/model/Password.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/model/UserCredentials.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/Layout.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/ViewSession.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/Login.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/Register.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/view/DateTime.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/exceptions/Exception.php');

use AuthComponent\Controller\Main as MainController;

/**
 * Main class for the complete AuthComponent
 *
 * Instaniate an object of this class to get an object and run renderComponent() for the component to be displayed.
 * Some exceptions will be thrown from this component that are not interally catched that needs to be catched from the caller.
 * 
 */
class AuthComponent {
  private \Settings $settings;
  private MainController $mainController;

  /**
   * Constructor for the class
   *
   * Important to give a Settings object when creating this object. The settings object must create a PDO database connection,
   * and a getDBConnection() method to get the database connection. It must also contain a prefix which will be used to create
   * a seperate named table name and the prefix should be possible to call with a getPrefix method.
   * @param Settings $settings - a settings object must be provided. Must have getPrefix() and getDBConnection() methods.
   * 
   */
  public function __construct(\Settings $settings) {
    $this->settings = $settings;
    $this->mainController = new MainController($this->settings);
  }

  public function renderComponent () : void {
    $this->mainController->run();
  }

  /**
   * To check if a user have been authenticated
   *
   * Call this method if you want to check if a user have been authenticated to change state in your application based on that.
   * 
   */
  public function isUserAuthenticated () : bool {
    return $this->mainController->isUserAuthenticated();
  }

  /**
   * To get the authenticated username
   *
   * Please call this method only if isUserAuthenticated is true. Will throw error if that is not the case.
   * @throws Exception if the the isUserAuthenticated is false.
   * 
   */
  public function getAuthenticatedUser () : string {
    return $this->mainController->getAuthenticatedUser();
  } 
}

?>