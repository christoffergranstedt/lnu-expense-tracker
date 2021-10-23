<?php

namespace ExpenseTrackerComponent\Controller;

use Exception;
use ExpenseTrackerComponent\Model\Expenses as Expenses;
use ExpenseTrackerComponent\Model\Expense as Expense;
use ExpenseTrackerComponent\Model\ExpenseType as ExpenseType;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\Model\Description as Description;
use ExpenseTrackerComponent\Model\Amount as Amount;
use ExpenseTrackerComponent\Model\Currency as Currency;
use ExpenseTrackerComponent\View\Navbar as NavbarView;
use ExpenseTrackerComponent\View\AddExpense as AddExpenseView;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class AddExpense {
  private ViewSession $viewSession;
  private Expenses $userExpenses;
  private User $user;
  private NavbarView $navBarView;
  private AddExpenseView $addExpenseView;

  public function __construct (ViewSession $viewSession, Expenses $userExpenses, User $user, NavbarView $navBarView) {
    $this->viewSession = $viewSession;
    $this->userExpenses = $userExpenses;
    $this->user = $user;
    $this->navBarView = $navBarView;
    $this->addExpenseView = new AddExpenseView($this->viewSession);
  }

	/**
	* To display the add expense form.
  * Will catch eventual exceptions and set message to show the exception message and exit the execution.
	*/
  public function displayAddExpenseForm () : void {
    try {
      $this->navBarView->render();
      $this->addExpenseView->render();
      $this->viewSession->clearMessage();
    } catch (\Exception $e) {
      // TODO: Update the handling of the exceptions. Right now exception messages that are not intended for client can be sent to the view.
      $this->displayErrorMessageAndExit($e);
    }
  }

	/**
	* Call if the user wants to add expense. 
  * Will get the entered form values from the session and create different objects for each value. Will store all these objects in the Expense object.
  * The expense object till be stored in the expenses object for the user togheter with the other expenses.
  * Will catch eventual exceptions and set message to show the exception message and exit the execution.
	*/
  public function processAddExpense () : void {
    try {
      $this->addExpenseView->setEnteredFormValuesInSession();
      $expense = $this->getExpenseDataOfEnteredValues();
      $this->userExpenses->addExpense($expense);
      $this->viewSession->setMessageToShowExpenseAdded();
      $this->addExpenseView->clearEnteredFormValuesInSession();

    } catch (\Exception $e) {
      // TODO: Update the handling of the exceptions. Right now exception messages that are not intended for client can be sent to the view.
      $this->displayErrorMessageAndExit($e);
    }
  }

  public function isRequestingToAddExpense () : bool {
    return $this->addExpenseView->isRequestingAddExpense();
  }

  private function getExpenseDataOfEnteredValues () : Expense {
    $description = new Description($this->viewSession->getEnteredDescription());
    $amount = new Amount($this->viewSession->getEnteredAmount());
    $currency = new Currency($this->viewSession->getEnteredCurrency());
    $expenseType = new ExpenseType($this->viewSession->getEnteredExpenseType());
    return new Expense($this->user, $description, $amount, $currency, $expenseType);
  }

  private function displayErrorMessageAndExit (Exception $e) : void {
    $this->viewSession->setMessageToShow($e->getMessage());
    $this->navBarView->render();
    $this->addExpenseView->render();
    $this->viewSession->clearMessage();
    exit();
  }
}
  
?>