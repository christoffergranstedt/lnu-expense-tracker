<?php

namespace ExpenseTrackerComponent\Model;

use \ExpenseTrackerComponent\WrongValueException as WrongValueException;
use \ExpenseTrackerComponent\ContainsIllegalCharException as ContainsIllegalCharException;

class Currency {
  // Hard-coding SEK, but possible to change this dynamically in feature.For the moment the currency always should be SEK.
  public string $defaultCurrency = 'SEK';
  public string $currency;

  public function __construct (string $currency) {
    if ($currency !== $this->defaultCurrency) {
      throw new WrongValueException("The currency is not of the expected default value $this->defaultCurrency");
    }

    $hasSpecialChars = preg_match('/[^a-zA-Z\d]/', $currency);
    if ($hasSpecialChars) {
      throw new ContainsIllegalCharException("String for description contains special characters that are not allowed");
    }
    
    $this->currency = $currency; 
  }
}

?>