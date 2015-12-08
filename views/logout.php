<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Logout', Translation::translate('Logout'), 'logout.html');
Account::logout();
View::render();
