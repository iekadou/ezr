<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

$program_id = isset($_GET['program_id']) ? htmlspecialchars($_GET['program_id']) : false;
$Program = new Program();
if ($program_id == 'new' && Account::is_logged_in()) {
    $Program = $Program->set_name('new program')->set_userid(Account::get_user_id())->create();
    header("Location: ".UrlsPy::get_url('program', $Program->get_id()));
} else {
    $Program = $Program->get($program_id);
}
if ($Program == false) {
    raise404();
    die();
}

new View('Program', Translation::translate('Program'), "program.html");
Globals::set_var('Program', $Program);

View::render();