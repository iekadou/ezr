<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

if (Account::is_logged_in() != true) {
    raise404();
    die();
}

new View('Profile', Translation::translate('Profile'), 'account/profile.html');
Globals::set_var('profile_active', true);
View::render();
