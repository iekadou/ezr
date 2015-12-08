<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Login', Translation::translate('Login'), "login.html");
$referrer = (isset($_GET['referrer']) ? htmlspecialchars($_GET['referrer']) : false);
Globals::set_var('referrer', $referrer);

View::render();
