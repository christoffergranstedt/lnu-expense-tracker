<?php 

namespace ExpenseTrackerComponent\View;

use ExpenseTrackerComponent\View\ViewSession as ViewSession;
use ExpenseTrackerComponent\Model\ExpenseType as ExpenseType;

class AddExpense {
  private static string $dateId = 'ExpenseAddForm::dateId';
  private static string $descriptionId = 'ExpenseAddForm::descriptionId';
  private static string $amountId = 'ExpenseAddForm::amountId';
  private static string $currencyId = 'ExpenseAddForm::currencyId';
  private static string $expenseTypeId = 'ExpenseAddForm::expenseTypeId';
  private static string $addExpense = 'Add_Expense';
  private static string $defaultCurrency = 'SEK';

  private ViewSession $viewSession;
  private array $expenseTypes;

  public function __construct (ViewSession $viewSession) {
    $this->viewSession = $viewSession;
    $expenseType = new ExpenseType();
    $this->expenseTypes = $expenseType->expenseTypes;
  }

	/**
	* Renders add exense form. 
  * Always gets the messages stored in session that can either be empty or not and return this message in the HTML String togheter with the other generated HTML.
  * If there are values for this inputs stored in session they will be populated in the render.
  * A dropdown of the different expense types that are provided in the expenseType object.
	*/
  public function render () : void {
		echo '
      <h1>Add a new expense to your list</h1>
			<form method="post" enctype="multipart/form-data"> 
				<fieldset>
					<legend>Add a new expense</legend>
					<p class="message">' . $this->viewSession->getMessageToShow() . '</p>
					
					<label for="' . self::$dateId . '">Date :</label>
					<input type="date" id="' . self::$dateId . '" name="' . self::$dateId . '" value="' . $this->viewSession->getEnteredDate() . '" />
          <br/>

					<label for="' . self::$descriptionId . '">Description :</label>
					<input type="text" id="' . self::$descriptionId . '" name="' . self::$descriptionId . '" value="' . $this->viewSession->getEnteredDescription() . '" />
          <br/>

					<label for="' . self::$amountId . '">Amount :</label>
					<input type="number" id="' . self::$amountId . '" name="' . self::$amountId . '" value="' . $this->viewSession->getEnteredAmount() . '"/>
          <br/>

					<label for="' . self::$currencyId . '">Currency :</label>
					<input disabled class="currency" type="text" id="' . self::$currencyId . '" name="' . self::$currencyId . '" value="' . self::$defaultCurrency . '"/>
          <br/>

					' . $this->getGeneratedDropDownEventTypes() . '

					<input id="submit" type="submit" name="' . self::$addExpense . '" value="Register" />
          <br/>
				</fieldset>
			</form>
		';
  }

  public function isRequestingAddExpense () : bool {
    return isset($_POST[self::$addExpense]);
  }

  public function setEnteredFormValuesInSession () : void {
    if (isset($_POST[self::$dateId])) $this->viewSession->setEnteredDate($_POST[self::$dateId]);
    if (isset($_POST[self::$descriptionId])) $this->viewSession->setEnteredDescription($_POST[self::$descriptionId]);
    if (isset($_POST[self::$amountId])) $this->viewSession->setEnteredAmount($_POST[self::$amountId]);
    $this->viewSession->setEnteredCurrency(self::$defaultCurrency); // Observe this value is not dynamic and will always be SEK. Keep it for easy feature update in future.
    if (isset($_POST[self::$expenseTypeId])) $this->viewSession->setEnteredEventType($_POST[self::$expenseTypeId]);
  }

  public function clearEnteredFormValuesInSession () : void {
    $this->viewSession->setEnteredDate('');
    $this->viewSession->setEnteredDescription('');
    $this->viewSession->setEnteredAmount('');
    $this->viewSession->setEnteredCurrency('');
    $this->viewSession->setEnteredEventType('');
  }

  private function getGeneratedDropDownEventTypes () : string {
    $HTMLString = '
      <label for="' . self::$expenseTypeId . '">Choose type of expense:</label>
      <select id="' . self::$expenseTypeId . '" name="' . self::$expenseTypeId . '">
        <option value="" selected disabled hidden>Choose..</option>
    ';

    for ($i = 0; $i < count($this->expenseTypes); $i++) {
      $HTMLString .= '<option value="' . $this->expenseTypes[$i] . '">' . $this->expenseTypes[$i] . '</option>';
    }
  
    $HTMLString .= '</select>';
    return $HTMLString;    
  }
}

?>