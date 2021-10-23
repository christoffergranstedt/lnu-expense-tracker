<?php
// Load local environment variables
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/env.php')) {
  require_once($_SERVER["DOCUMENT_ROOT"] . '/env.php');
}

require_once($_SERVER["DOCUMENT_ROOT"] . '/Settings.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/Environment.php');
$environment = new Environment();
$settings = new Settings();

// In development show errors and in production specfic set session cookie to secure
if (!$environment->isProductionEnvironment()) {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
} else {
  ini_set( 'session.cookie_secure', 1 );
}
ini_set( 'session.cookie_httponly', 1 );
ini_set( 'session.cookie_samesite', 'lax');

session_start();

require_once($_SERVER["DOCUMENT_ROOT"] . '/controller/Application.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/view/View.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/AuthComponent/AuthComponent.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/components/ExpenseTrackerComponent/ExpenseTrackerComponent.php');

$expenseTrackerApplication = new \ExpenseTrackerApplication\Controller\Application($settings);
$expenseTrackerApplication->run();
?>
