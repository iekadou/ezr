<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        raise404();
        die();
    }

    $Shader = new Shader();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }

    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Shader = $Shader->get_by(array(array("id", "=", $id), array("userid", "=", Account::get_user_id())));
            try { $Shader = $Shader->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Shader == false) {
                raise404();
                die();
            }
            if ($Shader->save()) {
                echo '{"url": "'.UrlsPy::get_url('account').'"}';
                die();
            }
            break;
        case "POST":
            if (Account::get_user()->is_activated() != true) {
                raise404();
                die();
            }
            $Shader = $Shader->set_userid(Account::get_user_id());
            try { $Shader = $Shader->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }

            if ($Shader->create()) {
                echo '{"url": "'.UrlsPy::get_url('account').'"}';
                die();
            }
            break;
        default:
            raise404();
            die();
    }
    throw new ValidationError(array());
} catch (ValidationError $e) { echo $e->stringify(); die(); }
