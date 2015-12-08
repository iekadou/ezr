<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

if (Account::is_logged_in() != true) {
    raise404();
    die();
}

new View('Dashboard', Translation::translate('Dashboard'), 'account/index.html');
Globals::set_var('dashboard_active', true);
View::render();
