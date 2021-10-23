<?php

namespace AuthComponent;

class LoginFailException extends \Exception {};
class LoginWrongUserAgentInSessionException extends \Exception {};
class LoginWrongCookieException extends \Exception {};
class PasswordIsEmptyException extends \Exception {};
class PasswordIsToShortException extends \Exception {};
class PasswordNotMatchException extends \Exception {};
class RegisterUserExistException extends \Exception {};
class UsernameContainsIllegalCharException extends \Exception {};
class UsernameIsEmptyException extends \Exception {};
class UsernameIsToShortException extends \Exception {};
class UserIsNotAuthorizedException extends \Exception {};

?>