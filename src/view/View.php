<?php

namespace ExpenseTrackerApplication\View;

use AuthComponent\Authcomponent as Authcomponent;
use ExpenseTrackerComponent\ExpenseTrackerComponent as ExpenseTrackerComponent;

class View {
  public function renderAuth (Authcomponent $authcomponent) : void {
    $this->renderHeadHTML();
    $this->renderAuthComponent($authcomponent);
    $this->renderFooterHTML();
  }

  public function render (Authcomponent $authcomponent, ExpenseTrackerComponent $expenseTrackerComponent, string $username) : void {
    $this->renderHeadHTML();
    $this->renderAuthComponent($authcomponent);
    $this->renderExpenseTrackerComponent($expenseTrackerComponent, $username);
    $this->renderFooterHTML();
  }

  private function renderHeadHTML () : void {
    echo '<!DOCTYPE html> 
      <html>
        <head>
          <meta charset="utf-8">
          <title>Expense Tracker Application</title>
          <link rel="stylesheet" href="styles.css" type="text/css">
        </head>
        <body>
    ';
  }

  public function renderError () : void {
    $this->renderHeadHTML();
    $this->renderErrorMessage();
    $this->renderFooterHTML();
  }

  private function renderAuthComponent (Authcomponent $authcomponent) : void {
    $authcomponent->renderComponent();
  }

  private function renderExpenseTrackerComponent (ExpenseTrackerComponent $expenseTrackerComponent, string $username) : void {
    $expenseTrackerComponent->renderComponent($username);
  }

  private function renderErrorMessage () {
    echo '
      <p>Sorry, there is an internal error</p>
      <a href="/">Go back to start page</a>    
    ';
  }

  private function renderFooterHTML () : void {
    echo '
        </body>
      </html>
    ';
  }
}

?>