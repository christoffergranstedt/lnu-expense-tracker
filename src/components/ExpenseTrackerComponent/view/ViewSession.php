<?php

namespace ExpenseTrackerComponent\View;

class ViewSession {
  private static string $cliName = 'cli';
	private static string $PHPVersion = '5.4.0';
  private static string $messageId = 'ExpenseTrackerComponent::messageId';
  private static string $descriptionId = 'ExpenseTrackerComponent::descriptionId';
  private static string $amountId = 'ExpenseTrackerComponent::amountId';
  private static string $currencyId = 'ExpenseTrackerComponent::currencyId';
  private static string $eventTypeId = 'ExpenseTrackerComponent::eventTypeId';

  private static string $messageExpenseAdded = 'Expense is created and added to your other expenses';
  private static string $messageNoExpenses = 'You have not added any expenses yet, please add one';

	/**
	* Constructor for the class, that need to have a session started to run.
	* @throws Exception if there are no session started it will throw an error since the application is dependent on this to properly run.
	*/
  public function __construct() {
    if (!$this->isSessionStarted()) {
			throw new \Exception('Session is not started');
		}
  }

	//https://www.php.net/manual/en/function.session-status.php
	private function isSessionStarted () : bool {
    if ( php_sapi_name() !== self::$cliName ) {
      if ( version_compare(phpversion(), self::$PHPVersion, '>=') ) {
        return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
      } else {
        return session_id() === '' ? FALSE : TRUE;
      }
    }
    return FALSE;
	}

  public function getMessageToShow () : string {
    $message = isset($_SESSION[self::$messageId]) ? $_SESSION[self::$messageId] : '';
    return (string)$message;
  }

  public function setMessageToShow (string $message) : void {
    $_SESSION[self::$messageId] = $message;
  }

  public function setMessageToShowExpenseAdded () : void {
    $_SESSION[self::$messageId] = self::$messageExpenseAdded;
  }

  public function setMessageToShowNoExpenses () : void {
    $_SESSION[self::$messageId] = self::$messageNoExpenses;
  }

  public function clearMessage () : void {
    unset($_SESSION[self::$messageId]);
  }

  public function setEnteredDescription (string $description) : void {
		$_SESSION[self::$descriptionId] = $this->removeSpecialCharactersFromString($description);
	}

  public function getEnteredDescription () : string {
		return isset($_SESSION[self::$descriptionId]) ? $_SESSION[self::$descriptionId] : '';
	}

  public function setEnteredAmount (string $amount) : void {
		$_SESSION[self::$amountId] = $this->removeSpecialCharactersFromString($amount);
	}

  public function getEnteredAmount () : string {
		return isset($_SESSION[self::$amountId]) ? $_SESSION[self::$amountId] : '';
	}

  // Not currently in use, but I keep it here for feature improvement. To get a dynamically currency entered from user the summary over the expenses..
  // ..with different currencies most be taken care of also.
  public function setEnteredCurrency (string $currency) : void {
		$_SESSION[self::$currencyId] = $this->removeSpecialCharactersFromString($currency);
	}

  // Not currently in direct use, but I keep it here for feature improvement. To get a dynamically currency entered from user the summary over the expenses..
  // ..with different currencies most be taken care of also.
  public function getEnteredCurrency () : string {
		return isset($_SESSION[self::$currencyId]) ? $_SESSION[self::$currencyId] : '';
	}

  public function setEnteredEventType (string $eventType) : void {
		$_SESSION[self::$eventTypeId] = $this->removeSpecialCharactersFromString($eventType);
	}

  public function getEnteredExpenseType () : string {
    return isset($_SESSION[self::$eventTypeId]) ? $_SESSION[self::$eventTypeId] : '';
  }

  private function removeSpecialCharactersFromString (string $value) : string {
		$strippedValue = strip_tags($value);
		return preg_replace('/[^a-zA-Z0-9_ -]/s','',$strippedValue);
	}
}

?>