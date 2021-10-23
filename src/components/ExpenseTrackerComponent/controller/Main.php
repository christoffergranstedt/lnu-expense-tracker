<?php

namespace ExpenseTrackerComponent\Controller;

use ExpenseTrackerComponent\Controller\ExpensesOverview as ExpensesOverviewController;
use ExpenseTrackerComponent\Controller\AddExpense as AddExpenseController;
use ExpenseTrackerComponent\Controller\ExpensesSummary as ExpensesSummaryController;
use ExpenseTrackerComponent\Model\Expenses as Expenses;
use ExpenseTrackerComponent\Model\DAL\ExpenseDAL as ExpenseDAL;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\View\Navbar as NavbarView;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class Main {
  private \Settings $settings;
  private NavbarView $navbarView;
  private ExpensesOverviewController $expenseOverviewController;
  private AddExpenseController $addExpenseController;
  private Expenses $userExpenses;
  private ViewSession $viewSession;
  private ExpensesSummaryController $expensesSummaryController;

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
    $this->navbarView = new NavbarView();
    $this->viewSession = new ViewSession();
  }

	/**
	* The main method to run the component.
  * Will create a User object with the $username that must be provided in param.
  * Will listen to user interactions when user is posting form or changing url and will change the state depending
  * on that and render the correct output.
  * @param string $username - An authenticated user
	*/
  public function run (string $username) : void {
    $user = new User($username);
    $this->setupDBConnectionAndTables($user);
    $this->setupControllers($user);
    $this->listenToUserInteractions();
    $this->displayPages();
  }

  private function setupDBConnectionAndTables (User $user) : void {
    $dbConnection = $this->settings->getDBConnection();
    $this->expenseDAL = new ExpenseDAL($dbConnection);
    $this->userExpenses = new Expenses($this->expenseDAL, $user);
  }

  private function setupControllers (User $user) : void {
    $this->expenseOverviewController = new ExpensesOverviewController($this->viewSession, $this->userExpenses, $user, $this->navbarView);
    $this->addExpenseController = new AddExpenseController($this->viewSession, $this->userExpenses, $user, $this->navbarView);
    $this->expensesSummaryController = new ExpensesSummaryController($this->viewSession, $this->userExpenses, $user, $this->navbarView);
  }

  private function listenToUserInteractions () : void {
    if ($this->addExpenseController->isRequestingToAddExpense()) {
      $this->addExpenseController->processAddExpense();
      $this->redirectToIndex();
    }
  }

  /**
	* Will render the output based on the state changes that will have happened in listenToUserInteractions.
  * Will call different controllers depending if the user if the user is requesting to show the add expense form,
  * is requesting to view the all summaries for the expense or to list all added expenses and will call different 
  * controllers depending on that.
	*/
  private function displayPages () : void {
    if ($this->navbarView->isRequestingToShowAddExpenseForm()) {
      $this->addExpenseController->displayAddExpenseForm();

    } else if ($this->navbarView->isRequestingToShowViewSummary()) {
      $this->expensesSummaryController->displayExpensesSummary();

    } else {
      $this->expenseOverviewController->displayExpensesOverview();
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
  
