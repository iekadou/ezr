<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You have to be logged in to save!').'"}]}';
        die();
    }

    $ShaderPass = new ShaderPass();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $ShaderPass = $ShaderPass->get_by(array(array("id", "=", $id), array("userid", "=", Account::get_user_id())));
            if ($ShaderPass == false) {
                raise404();
                die();
            }
            if ($ShaderPass->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only save your own stuff!').'"}]}';
                die();
            }
            try { $ShaderPass = $ShaderPass->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($ShaderPass->save()) {
                echo '{"url": "'.UrlsPy::get_url('account').'"}';
                die();
            }
            break;
        case "POST":
            if (Account::get_user()->is_activated() != true) {
                raise404();
                die();
            }
            $ShaderPass = $ShaderPass->set_userid(Account::get_user_id());
            try { $ShaderPass = $ShaderPass->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
//            print_r($ShaderPass->get_program_id());
            if ($ShaderPass->create()) {
                new View('', '', '_include/_shader_pass.html');
                View::set_template_var('shaderPass', $ShaderPass);
                View::render();
                die();
            }
            break;
        default:
            print_r(REQUEST_METHOD);
//            raise404();
//            die();
    }
    throw new ValidationError(array());
} catch (ValidationError $e) { echo $e->stringify(); die(); }
