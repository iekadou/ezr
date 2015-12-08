<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Forgot password', Translation::translate('Forgot password'), "forgot_password.html");
View::render();
