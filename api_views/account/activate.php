<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

    try {
        if (Account::is_logged_in() != true) {
            raise404();
            die();
        }
        Account::get_user()->send_activation_email();
        echo '{"url": "'.UrlsPy::get_url('account').'"}'; die();
    } catch (ValidationError $e) { echo $e->stringify(); die(); }
