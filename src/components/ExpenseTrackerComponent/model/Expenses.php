<?php

namespace ExpenseTrackerComponent\Model;

use ExpenseTrackerComponent\Model\DAL\ExpenseDAL as ExpenseDAL;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\Model\ExpenseType as ExpenseType;
use ExpenseTrackerComponent\Model\ExpenseSummary as ExpenseSummary;

class Expenses {
  private ExpenseDAL $expenseDAL;
  private User $user;
  private array $userExpenses;

  public function __construct (ExpenseDAL $expenseDAL, User $user) {
    $this->expenseDAL = $expenseDAL;
    $this->user = $user;
    $this->userExpenses = $this->expenseDAL->getAllExpensesByUsername($this->user->getUsername());
  }

  public function getAllExpenses () : array {
    $newArray = $this->userExpenses;
    return $newArray;
  }

  /**
	* To add the expense type. Will save it both to the database and private expense array in this object. 
	*/
  public function addExpense (Expense $expense) {
    $this->userExpenses[] = $expense;
    $this->expenseDAL->saveExpense($expense);
  }

  public function getUsername () : string {
    return $this->username->username;
  }

  public function getExpensesSummaries () : array {
    // Create a expensetype to get the available expense types
    $expenseType = new ExpenseType();
    $expenseTypes = $expenseType->expenseTypes;

    // Create a exmpty array were all ExpenseSummary object will be stored.
    // For every expense type get the total amount for that event type and then create the ExpenseSummary with that total amount and the expenstype and then return with it.
    $expenseSummaries = [];
    for ($i = 0; $i < count($expenseTypes); $i++) {
      $totalAmount = $this->getTotalAmountByExpenseType($expenseTypes[$i]);
      $expenseSummary = new ExpenseSummary($expenseTypes[$i], $totalAmount);
      $expenseSummaries[] = $expenseSummary;
    }
    return $expenseSummaries;
  }

  private function getTotalAmountByExpenseType (string $expenseType) : int {
    // Loops over the user expenses in this object for the user and if a
    // certain expense type matches the one provided in param add it to the total amount.
    $totalAmount = 0;
    for ($i = 0; $i < count($this->userExpenses); $i++) {
      if ($this->userExpenses[$i]->getExpenseType() === $expenseType) {
        $totalAmount += $this->userExpenses[$i]->getAmount();
      }
    }
    return $totalAmount;
  }
}

?>