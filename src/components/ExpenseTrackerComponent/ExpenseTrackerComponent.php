<?php

namespace ExpenseTrackerComponent;

require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/controller/Main.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/controller/AddExpense.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/controller/ExpensesSummary.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/controller/ExpensesOverview.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/controller/ExpensesSummary.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/DAL/ExpenseDAL.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/User.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/Expenses.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/Expense.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/ExpenseType.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/ExpenseSummary.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/Description.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/Amount.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/model/Currency.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/view/Navbar.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/view/AddExpense.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/view/ExpensesOverview.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/view/ExpensesSummary.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/view/ViewSession.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/exceptions/Exception.php');

use ExpenseTrackerComponent\Controller\Main as MainController;

class ExpenseTrackerComponent {
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

  public function renderComponent (string $username) : void {
    $this->mainController->run($username);
  }
}

?>