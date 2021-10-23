<?php

namespace AuthComponent\Model;

class UserCredentials {
  private Username $username;
  private Password $password;
  private bool $hasKeepLoggedInChecked;

  public function __construct (Username $username, Password $password, bool $hasKeepLoggedInChecked = false) {
    $this->username = $username;
    $this->password = $password;
    $this->hasKeepLoggedInChecked = $hasKeepLoggedInChecked;
  }

  public function getUsername () : string {
    return $this->username->username;
  }

  public function getPassword () : string {
    return $this->password->password;
  }

  public function hasKeepLoggedInChecked () : bool {
    return $this->hasKeepLoggedInChecked;
  }
}

?>