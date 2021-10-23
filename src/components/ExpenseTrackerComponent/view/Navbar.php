<?php 

namespace ExpenseTrackerComponent\View;

class Navbar {
  private static string $addExpenseRoute = 'add_expense';
  private static string $viewExpenseSummaryRoute = 'view_expense_summary';

  public function render () : void {
    echo '
      <div class="nav">
        <ul>
          <li><a href="/index.php">All expenses</a></li>
          <li><a href="/index.php?' . self::$addExpenseRoute .'">Add new expense</a></li>
          <li><a href="/index.php?' . self::$viewExpenseSummaryRoute .'">Summary of expenses</a></li>
        </ul>
      </div>
    ';
  }

  public function isRequestingToShowAddExpenseForm () : bool {
    return isset($_GET[self::$addExpenseRoute]);
  } 

  public function isRequestingToShowViewSummary () : bool {
    return isset($_GET[self::$viewExpenseSummaryRoute]);
  } 
}

?>