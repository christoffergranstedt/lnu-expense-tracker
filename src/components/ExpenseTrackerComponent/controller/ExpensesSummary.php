<?php

namespace ExpenseTrackerComponent\Controller;

use ExpenseTrackerComponent\Model\Expenses as Expenses;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\View\Navbar as NavbarView;
use ExpenseTrackerComponent\View\ExpensesSummary as ExpensesSummaryView;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class ExpensesSummary {
  private ViewSession $viewSession;
  private Expenses $userExpenses;
  private NavbarView $navbarView;
  private ExpensesSummaryView $expensesSummaryView;

  public function __construct (ViewSession $viewSession, Expenses $userExpenses, User $user, NavbarView $navbarView) {
    $this->viewSession = $viewSession;
    $this->userExpenses = $userExpenses;
    $this->user = $user;
    $this->navbarView = $navbarView;
    $this->expensesSummaryView = new ExpensesSummaryView($this->viewSession);
  }

	/**
	* To display the expenses summary.
  * Will make a request to get all the ExpenseSummary objects stored in an array and render the view togheter with that array.
  * Will catch eventual exceptions and set message to show the exception message and exit the execution.
	*/
  public function displayExpensesSummary () : void {
    try {
      $expenseSummaries = $this->userExpenses->getExpensesSummaries();
      $this->navbarView->render();
      $this->expensesSummaryView->render($expenseSummaries);
      $this->viewSession->clearMessage();
    } catch (\Exception $e) {
      // TODO: Update the handling of the exceptions. Right now exception messages that are not intended for client can be sent to the view.
      $this->displayErrorMessageAndExit($e);
    }
  }

  private function displayErrorMessageAndExit (\Exception $e) : void {
    $this->viewSession->setMessageToShow($e->getMessage());
    $this->navBarView->render();
    $this->expensesSummaryView->render();
    $this->viewSession->clearMessage();  
    exit();
  }
}

?>
  
