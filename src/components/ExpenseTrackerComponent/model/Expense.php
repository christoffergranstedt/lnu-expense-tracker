<?php

namespace ExpenseTrackerComponent\Model;

use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\Model\Description as Description;
use ExpenseTrackerComponent\Model\Currency as Currency;
use ExpenseTrackerComponent\Model\Amount as Amount;
use ExpenseTrackerComponent\Model\ExpenseType as ExpenseType;

class Expense {
  private User $user;
  private Description $description;
  private Amount $amount;
  private Currency $currency;
  private ExpenseType $expenseType;

  public function __construct (User $user, Description $description, Amount $amount, Currency $currency, ExpenseType $expenseType) {
    $this->user = $user;
    $this->description = $description;
    $this->amount = $amount;
    $this->currency = $currency;
    $this->expenseType = $expenseType;
  }

  public function getUsername () : string {
    return $this->user->getUsername();
  }

  public function getDescription () : string {
    return $this->description->description;
  }

  public function getAmount () : int {
    return $this->amount->amount;
  }

  public function getCurrency () : string {
    return $this->currency->currency;
  }

  public function getExpenseType () : string {
    return $this->expenseType->chosenExpenseType;
  }
  
}

?>