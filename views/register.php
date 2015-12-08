<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Register', Translation::translate('Register'), 'register.html');
View::render();
