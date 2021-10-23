<?php

namespace ExpenseTrackerComponent\Controller;

use ExpenseTrackerComponent\Model\Expenses as Expenses;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\View\Navbar as NavbarView;
use ExpenseTrackerComponent\View\ExpensesOverview as ExpensesOverviewView;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class ExpensesOverview {
  private ViewSession $viewSession;
  private Expenses $userExpenses;
  private User $user;
  private NavbarView $navbarView;
  private ExpensesOverviewView $expenseOverviewView;

  public function __construct (ViewSession $viewSession, Expenses $userExpenses, User $user, NavbarView $navbarView) {
    $this->viewSession = $viewSession;
    $this->userExpenses = $userExpenses;
    $this->user = $user;
    $this->navbarView = $navbarView;
    $this->expenseOverviewView = new ExpensesOverviewView($this->viewSession);
  }

	/**
	* To display the all the expenses for a user.
  * Will make a request to get all the Expense objects stored in an array and render the view togheter with that array.
  * Will catch eventual exceptions and set message to show the exception message and exit the execution.
	*/
  public function displayExpensesOverview () : void {
    try {
      $this->setUserExpenseStatusInView();
      $this->navbarView->render();
      $this->expenseOverviewView->render($this->user->getUsername());
      $this->viewSession->clearMessage();
    } catch (\Exception $e) {
      // TODO: Update the handling of the exceptions. Right now exception messages that are not intended for client can be sent to the view.
      $this->displayErrorMessageAndExit($e);
    }
  }

  private function setUserExpenseStatusInView () : void {
    // Get all the userExpenses from the expenses-object. If there are 0 expenses
    // show a message to the user so it knows that.
    $userExpenses = $this->userExpenses->getAllExpenses();
    if (count($userExpenses) === 0) {
      $this->viewSession->setMessageToShowNoExpenses();
    }
    $this->expenseOverviewView->setExpensesToShow($userExpenses);
  }

  private function displayErrorMessageAndExit (\Exception $e) : void {
    $this->viewSession->setMessageToShow($e->getMessage());
    $this->navBarView->render();
    $this->expenseOverviewView->render($this->user->getUsername());
    $this->viewSession->clearMessage();
    exit();
  }
}
  
?>