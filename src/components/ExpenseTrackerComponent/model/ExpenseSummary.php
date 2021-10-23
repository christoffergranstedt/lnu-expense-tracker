<?php

namespace ExpenseTrackerComponent\Model;

class ExpenseSummary {
  public string $expenseType;
  public int $totalAmount;

  public function __construct (string $expenseType, int $totalAmount) {
    $this->expenseType = $expenseType;
    $this->totalAmount = $totalAmount;
  }
}

?>