<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Activate', Translation::translate('Activate'), "activate.html");

$User = new User();
$activation_key = (isset($_GET['activation_key']) ? htmlspecialchars($_GET['activation_key']) : false);
$User = $User->get_by(array(array("activation_key", "=", $activation_key)));

if ($User == false) {
    raise404();
    die();
}
$User->activate()->save();

View::render();
