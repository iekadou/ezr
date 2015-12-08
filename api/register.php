<?php
    namespace Iekadou\Webapp;
    require_once("../inc/include.php");

    $User = new User();
    try { $User->interpret_user($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
    try { $User->create(); } catch (ValidationError $e) { echo $e->stringify(); die(); }
    try { $User->send_activation_email(); } catch (ValidationError $e) { echo $e->stringify(); die(); }
    try { Account::login($User, $password=null); } catch (ValidationError $e) { echo $e->stringify(); die(); }

    echo '{"url": "'.UrlsPy::get_url('account').'"}';
