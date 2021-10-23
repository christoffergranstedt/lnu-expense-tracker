<?php

class Environment {
  private static string $ENVIRONMENT = 'ENVIRONMENT';
  private static string $PRODUCTION = 'PRODUCTION';
  private static string $DB_SERVERNAME = 'DB_SERVERNAME';
  private static string $DB_USER = 'DB_USER';
  private static string $DB_PASSWORD = 'DB_PASSWORD';
  private static string $DB_NAME = 'DB_NAME';
  private static string $DB_PORT = 'DB_PORT';

  public function isProductionEnvironment () : bool {
    return getenv(self::$ENVIRONMENT) === self::$PRODUCTION;
  }

  public function getDBServerName () : string {
    return getenv(self::$DB_SERVERNAME);
  }

  public function getDBUser () : string {
    return getenv(self::$DB_USER);
  }

  public function getDBPassword () : string {
    return getenv(self::$DB_PASSWORD);
  }

  public function getDBName () {
    return getenv(self::$DB_NAME);
  }

  public function getDBPort () {
    return getenv(self::$DB_PORT);
  }
}

?>