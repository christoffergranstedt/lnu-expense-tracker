<?php

class Settings {
  private \PDO $db;
  private static string $prefix = 'ExpenseTrackerApplication';

  public function __construct () {
    $this->createDBConnection();
  }

  public function createDBConnection () : void {
    $environment = new \Environment();
    $servername = $environment->getDBServerName();
    $username = $environment->getDBUser();
    $password = $environment->getDBPassword();
    $dbName = $environment->getDBName();
    $port = $environment->getDBPort();

    try {
      $dsn = "mysql:host=$servername;port=$port;dbname=$dbName";
      $this->db = new \PDO($dsn, $username, $password);
      $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
      $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $this->db;
    } catch (\Exception $e) {
      throw new \Exception('Problem setting up the database connection.');
    }
  }

  public function getDBConnection () : \PDO {
    return $this->db;
  }

  public function getPrefix () : string {
    return self::$prefix;
  }
}