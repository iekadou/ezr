<?php
    namespace Iekadou\Webapp;
    require_once("../inc/include.php");

    try {
        $userid = (isset($_POST['userid']) ? htmlspecialchars($_POST['userid']) : false);
        if ($userid != Account::get_user_id()) {
            $userid = false;
            $errors[] = "userid";
        }
        $User = new User();
        $User = $User->get($userid);
        $User = $User->interpret_user($_POST, $_FILES);
        if (!empty($errors)) {
            throw new ValidationError($errors);
        }
        $User->save();
        echo '{"url": "' . UrlsPy::get_url('account') . '"}';
    } catch (ValidationError $e) { echo $e->stringify(); die(); }
