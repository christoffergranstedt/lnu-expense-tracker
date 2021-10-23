<?php 

namespace ExpenseTrackerComponent\View;

use ExpenseTrackerComponent\Model\Expense as Expense;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class ExpensesOverView {

  private array $userExpenses;
  private ViewSession $viewSession;

  public function __construct (ViewSession $viewSession) {
    $this->userExpenses = [];
    $this->viewSession = $viewSession;
  }

	/**
	* Renders all users expenses that are stored in the userExpenses. This array included Expense object.
  * @param string $username - Provide username that will be shown in the headline
	*/
  public function render (string $username) : void {
    $HTMLString = '<h1>All expenses for ' . $username .  '</h1>';
    $HTMLString .= '<p class="message">' . $this->viewSession->getMessageToShow() . '</p>';
    $HTMLString .= '<div class="expenses-container">';

    for ($i = 0; $i < count($this->userExpenses); $i++) {
      $HTMLString .= $this->getExpenseHTML($this->userExpenses[$i]);
    }
    $HTMLString .= '</>';
    echo $HTMLString;
  }

  public function setExpensesToShow (array $userExpenses) : void {
    $this->userExpenses = $userExpenses;
  }

  private function getExpenseHTML (Expense $expense) : string {
    return '
      <div class="expense"> 
        <p>Description: ' . $expense->getDescription() . '</p>
        <p>Amount: ' . $expense->getAmount() . ' ' . $expense->getCurrency() . '</p>
        <p>Expense Category: ' . $expense->getExpenseType() . '</p>
      </div>      
    ';
  }
}

?>