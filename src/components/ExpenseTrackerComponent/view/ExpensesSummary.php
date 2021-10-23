<?php 

namespace ExpenseTrackerComponent\View;

use ExpenseTrackerComponent\Model\ExpenseSummary as ExpenseSummary;
use ExpenseTrackerComponent\View\ViewSession as ViewSession;

class ExpensesSummary {
  private ViewSession $viewSession;

  public function __construct (ViewSession $viewSession) {
    $this->viewSession = $viewSession;
  }

	/**
	* Renders different expense types and the total amount spent.
  * @param array $expenseSummaries - An array with expense summaries that will contain ExpenseSummary objects
	*/
  public function render (array $expenseSummaries = []) : void {
    $HTMLString = '<h1>Summary for each expense category</h1>';
    $HTMLString .= '<p class="message">' . $this->viewSession->getMessageToShow() . '</p>';
    $HTMLString .= '<div class="expenses-summaries-container">';

    for ($i = 0; $i < count($expenseSummaries); $i++) {
      $HTMLString .= $this->getSummaryHTML($expenseSummaries[$i]);
    }
    $HTMLString .= '</>';
    echo $HTMLString;
  }

  private function getSummaryHTML (ExpenseSummary $expenseSummary) : string {
    return '
      <div class="expense-summary"> 
        <p>Expense Category: ' . $expenseSummary->expenseType . '</p>
        <p>Total amount: ' . $expenseSummary->totalAmount . ' SEK</p>
      </div>      
    ';
  }

}

?>