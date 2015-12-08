<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

if (Account::is_logged_in() != true) {
    raise404();
    die();
}

new View('Activate', Translation::translate('Activate'), 'account/activate.html');
Globals::set_var('dashboard_active', true);
View::render();
