<?php

namespace ExpenseTrackerApplication\Controller;

use AuthComponent\AuthComponent as AuthComponent;
use ExpenseTrackerComponent\ExpenseTrackerComponent as ExpenseTrackerComponent;
use ExpenseTrackerApplication\View\View as View;

class Application {
  private AuthComponent $authComponent;
  private ExpenseTrackerComponent $expenseTrackerComponent;
  private View $view;

 /**
   * Constructor for the class
   *
   * Important to give a Settings object when creating this object. The settings object must create a PDO database connection,
   * and a getDBConnection() method to get the database connection. It must also contain a prefix which will be used to create
   * a seperate named table name and the prefix should be possible to call with a getPrefix method. This settings object will be 
   * included in different components to create different database tables depending of the component.
   * @param Settings $settings - a settings object must be provided. Must have getPrefix() and getDBConnection() methods.
   */
  public function __construct (\Settings $settings) {
    $this->authComponent = new AuthComponent($settings);
    $this->expenseTrackerComponent = new ExpenseTrackerComponent($settings);
    $this->view = new View();
  }

 /**
   * The main method that runs the whole Expense Tracker Application.
   * Will check if a user is authenticated in the authcomponent and if not render the Auth view where only the authcomponent is present.
   * If the user is authenticated a username will be fetched from the authcomponent and then renders a view were both the authcomponent and expensetrackercomponent is present.
   */
  public function run () : void {
    try {
      if (!$this->authComponent->isUserAuthenticated()) {
        $this->view->renderAuth($this->authComponent);
      } else {
        $username = $this->authComponent->getAuthenticatedUser();
        $this->view->render($this->authComponent, $this->expenseTrackerComponent, $username);
      }

    } catch (\Exception $e) {
      // TODO: Create a more visually appealing view when error occurs
      $this->view->renderError();
    }
  }
}

?>