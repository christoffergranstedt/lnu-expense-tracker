<?php

namespace AuthComponent\View;

use AuthComponent\View\DateTime as DateTimeView;
use AuthComponent\View\View as View;

class Layout {
  private static string $registerRoute = 'register';
  private DateTimeView $dateTimeView;
  private string $loggedInHTML;
  private string $registerHTML;
  private bool $isLoggedIn;
  private bool $isRegistering;


  public function __construct () {
    $this->dateTimeView = new DateTimeView();
    $this->isLoggedIn = false;
    $this->isRegistering = false;
  }

	/**
	* The main method for the view of the AuthComponent. Holds everything together. Is the only method that echos to the client. Handles if the user is logged in
  * or trying to register and will render different output depending on that.
  * @param View @renderView Is a view that implements the interface View. Will get the HTML string that is provided in separate views that are provided to this method.
	*/
  public function render (View $renderView) : void {
    $this->setLayoutHTMLBasedOfView();
    echo '
      <div class="login-container"> 
        ' . $this->registerHTML. '
        ' . $this->loggedInHTML . '
        
        <div class="container">
            ' . $renderView->getHTMLString() . '         
            ' . $this->dateTimeView->getHTMLString() . '
        </div>
      </div>
    ';
  }

  public function setIsLoggedIn (bool $isLoggedIn) : void {
    $this->isLoggedIn = $isLoggedIn;
  }

  public function setIsRegistering (bool $isRegistering) : void {
    $this->isRegistering = $isRegistering;
  }

  public function isRequestingRegisterForm () : bool {
    return isset($_GET[self::$registerRoute]);
  }

  private function setLayoutHTMLBasedOfView () : void {
    if ($this->isLoggedIn) {
      $this->loggedInHTML = '<h2 id="login-status">Logged in</h2>';
      $this->registerHTML = '';

    } elseif ($this->isRegistering) {
      $this->loggedInHTML = '<h2 id="login-status">Not logged in</h2>';
      $this->registerHTML = '<a href="/index.php">Back to login</a>';

    } else {
      $this->loggedInHTML = '<h2 id="login-status">Not logged in</h2>';
      $this->registerHTML = '<a href="/index.php?' . self::$registerRoute . '">Register a new user</a>';  
    }
  }
}

?>