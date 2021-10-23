<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\ExpenseTypeNotOfChosenTypesException as ExpenseTypeNotOfChosenTypesException;

class ExpenseType {

  public $expenseTypes = ['Housing', 'Food', 'Childcare', 'Debt', 'Health Care', 'Transportation',
    'Personal Care', 'Pet Care', 'Entertainment', 'Miscellaneous', 'Other'];
  public string $chosenExpenseType;

	/**
	* Instiates a ExpenseType.
  * @param string $chosenExpenseType - A string with the expense type. 
  * @throws ExpenseTypeNotOfChosenTypesException if the expensetype provided is not present in the $expenseTypes object.
	*/
  public function __construct (string $chosenExpenseType = 'Housing') {
    if(!in_array($chosenExpenseType, $this->expenseTypes)) {
      throw new ExpenseTypeNotOfChosenTypesException('Expense type is not of correct value');
    }

    $this->chosenExpenseType = $chosenExpenseType; 
  }
}

?>