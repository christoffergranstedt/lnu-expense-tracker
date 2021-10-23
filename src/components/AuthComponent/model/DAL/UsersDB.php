<?php

namespace AuthComponent\Model\DAL;

use AuthComponent\LoginFailException as LoginFailException;
use AuthComponent\LoginWrongCookieException as LoginWrongCookieException;
use AuthComponent\RegisterUserExistException as RegisterUserExistException;

class UsersDB {
  private static string $idId = 'id';
  private static string $usernameId = 'username';
  private static string $passwordId = 'password';
  private static string $passwordCookieId = 'password_cookie';
  private static string $passwordCookieExpirationDateId = 'password_cookie_expiration_date';
  private \PDO $dbConnection;
  private string $dbNameId;
  
  /**
	* Constructor for the user db. The object need to be provided with an external PDO db connection to properly work.
  * It is also important to provide a string with a prefix to the database. The table that is created will then be name <prefix>_users
  * to have a separate users table for possible multiple applications. 
  * @param PDO $dbConnection - A PDO database connection
  * @param string $prefix - A prefix that should be added to the database name.
	*/
  public function __construct(\PDO $dbConnection, string $prefix) {
    if ($prefix === null || mb_strlen($prefix) === 0) {
      throw new \Exception('The prefix to the users db is not set or an empty string, please choose a name');
    }
    $this->dbConnection = $dbConnection;
    $this->dbNameId = $prefix . '_users';
    $this->createTableIfNotExist();
  }

  /**
	* Checks if the provided username and non hashed password from inputs are the same as stored in the database.
  * It will transform the non hashed password to a hashed variant and compare this with the db.
  * @param string $username A username
  * @param string $password the non hashed password.
	*/
  public function authenticateByUsernameAndPassword (string $username, string $password) : void {
    $user = $this->getUserByUsername($username);
    if (!$user) {
      throw new LoginFailException('There is no user in the database with the username input in login form');
    }
    $isUserAuthenticated = password_verify($password, $user[self::$passwordId]);
    if (!$isUserAuthenticated) {
      throw new LoginFailException('The password input in login form does not match with the stored password in database');
    }
  }

  /**
	* Checks if the provided username and password the cookies are the the same as stored in the database.
  * @throws LoginWrongCookieException if no user with provided username can be found in the database or if the password expiration 
  * date has expired in the databse or if the password is not the same as the one stored in the database.
  * @param string $username A username from the cookie
  * @param string $password A password from the cookie.
	*/
  public function authenticateByCookies (string $username, string $passwordCookie) : void {
    $user = $this->getUserByUsername($username);
    if (!$user) {
      throw new LoginWrongCookieException('There is no user with the username stored in the cookies');
    }

    if (strtotime('now' > $user[self::$passwordCookieExpirationDateId])) {
      throw new LoginWrongCookieException('The password cookie expiration date in the database has expired');
    }

    if ($user[self::$passwordCookieId] !== $passwordCookie) {
      throw new LoginWrongCookieException('The password cookie stored on the database is not the same as the password cookie in the browser');
    } 
  }

  /**
	* Adds a new user to the database table. Must be provided with a UserCredentails object that holds the username and password input from the user.
  * It will first check that no user with same username already exits in database and will throw an Exception if it does. It will hash the password
  * and insert the username and password to the database.
  * @param UserCredentials Usercredentials object that need to contains the username and password
  * @throws RegisterUserExistException if the user already exist in database.
	*/
  public function addNewUser (\AuthComponent\Model\UserCredentials $userCredentials) : void {
    $this->isUsernameUnique($userCredentials->getUsername());
    $hashedPassword = $this->getHashedPassword($userCredentials->getPassword());
    $this->insertUser($userCredentials->getUsername(), $hashedPassword);
  }

  public function updatePasswordCookieInfo (string $username, string $token, int $tokenExpireDate) : void {
    $sql = "UPDATE " . $this->dbNameId . "
            SET " . self::$passwordCookieId . " = ?, " . self::$passwordCookieExpirationDateId . " = ?
            WHERE " . self::$usernameId . " = ?";
    $statement = $this->dbConnection->prepare($sql);
    $statement->execute([$token, $tokenExpireDate, $username]);
  }

  private function isUsernameUnique ($username) : bool {
    $user = $this->getUserByUsername($username);
    if ($user) {
      throw new RegisterUserExistException('Not able to register the new user since the username is already in use.');
    }
    return true;
  }

  private function getHashedPassword (string $password) : string {
    return password_hash($password, PASSWORD_BCRYPT);
  }

  private function insertUser (string $username, string $password) : void {
    $sql = "INSERT INTO " . $this->dbNameId . "(" . self::$usernameId . ", " . self::$passwordId . ") VALUES (?, ?)";
    $statement = $this->dbConnection->prepare($sql);
    $statement->execute([$username, $password]);
  }

  private function getUserByUsername ($username) {
    $sql = "SELECT * FROM " . $this->dbNameId . " WHERE BINARY " . self::$usernameId . " = ?";
    $statement = $this->dbConnection->prepare($sql);
    $statement->execute([$username]);
    return $statement->fetch();
  }

  private function createTableIfNotExist () : void {
    $sql = "CREATE TABLE IF NOT EXISTS " . $this->dbNameId . " (
              " . self::$idId . " int(11) AUTO_INCREMENT PRIMARY KEY,
              " . self::$usernameId . " varchar(30) NOT NULL UNIQUE,
              " . self::$passwordId . " varchar(2000) NOT NULL,
              " . self::$passwordCookieId . " varchar(200) UNIQUE,
              " . self::$passwordCookieExpirationDateId . " int(200)
            )";
    $this->dbConnection->exec($sql);
  }
}

?>