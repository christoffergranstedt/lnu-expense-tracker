<?php

namespace ExpenseTrackerComponent\Model\DAL;

use ExpenseTrackerComponent\Model\Expense as Expense;
use ExpenseTrackerComponent\Model\User as User;
use ExpenseTrackerComponent\Model\Date as Date;
use ExpenseTrackerComponent\Model\Description as Description;
use ExpenseTrackerComponent\Model\Amount as Amount;
use ExpenseTrackerComponent\Model\Currency as Currency;
use ExpenseTrackerComponent\Model\ExpenseType as ExpenseType;


class ExpenseDAL {
  private static string $dbNameId = 'expenses';
  private static string $idId = 'id';
  private static string $usernameId = 'username';
  private static string $dateId = 'date';
  private static string $descriptionId = 'description';
  private static string $amountId = 'amount';
  private static string $currencyId = 'currency';
  private static string $expenseTypeId = 'expense_type';
  private \PDO $dbConnection;
  
  /**
	* Constructor for the user db. The object need to be provided with an external PDO db connection to properly work.
  * @param PDO $dbConnection - A PDO database connection
	*/
  public function __construct(\PDO $dbConnection) {
    $this->dbConnection = $dbConnection;
    $this->createTableIfNotExist();
  }

  /**
	* Call this and get all expenses in the database for a certain user
  * Will loop over the result from database query and create different object and store this objects in the Expense Object.
  * @param string $username - The username with the expenses.
	*/
  public function getAllExpensesByUsername (string $username) : array {
    $expensesToReturn = [];
    $expensesFromDAL = $this->fetchExpensesByUsername($username);
    for ($i = 0; $i < count($expensesFromDAL); $i++) {
      $user = new User($expensesFromDAL[$i][self::$usernameId]);
      $date = new Date($expensesFromDAL[$i][self::$dateId]);
      $description = new Description($expensesFromDAL[$i][self::$descriptionId]);
      $amount = new Amount((string)$expensesFromDAL[$i][self::$amountId]);
      $currencyId = new Currency($expensesFromDAL[$i][self::$currencyId]);
      $expenseType = new ExpenseType($expensesFromDAL[$i][self::$expenseTypeId]);
      $expensesToReturn[] = new Expense($user, $date, $description, $amount, $currencyId, $expenseType);
    }
    return $expensesToReturn;
  }

  public function saveExpense (Expense $expense) : void {
    $sql = "INSERT INTO " . self::$dbNameId . "(" . self::$usernameId . ", " . self::$dateId . ", " . self::$descriptionId . ", " . self::$amountId  . ", " . self::$currencyId . ", " . self::$expenseTypeId . ") VALUES (?, ?, ?, ?, ?, ?)";
    $statement = $this->dbConnection->prepare($sql);
    $statement->execute([$expense->getUsername(), $expense->getDate(), $expense->getDescription(), $expense->getAmount(), $expense->getCurrency(), $expense->getExpenseType()]);
  }

  private function createTableIfNotExist () : void {
    $sql = "CREATE TABLE IF NOT EXISTS expenses (
      " . self::$idId . " int(11) AUTO_INCREMENT PRIMARY KEY,
      " . self::$usernameId . " varchar(30) NOT NULL,
      " . self::$dateId . " varchar(15),
      " . self::$descriptionId . " varchar(200),
      " . self::$amountId . " float(15),
      " . self::$currencyId . " varchar(30),
      " . self::$expenseTypeId . " varchar(50)
    )";
    $this->dbConnection->exec($sql);
  }

  private function fetchExpensesByUsername (string $username) : array {
    $sql = "SELECT * FROM " . self::$dbNameId . " WHERE BINARY " . self::$usernameId . " = ?";
    $statement = $this->dbConnection->prepare($sql);
    $statement->execute([$username]);
    return $statement->fetchAll();
  }
}

?>