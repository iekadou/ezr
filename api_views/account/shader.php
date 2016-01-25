<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You have to be logged in to save!').'"}]}';
        die();
    }

    $Shader = new Shader();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Shader = $Shader->get_by(array(array("id", "=", $id)));
            if ($Shader == false) {
                raise404();
                die();
            }
            if ($Shader->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only save your own stuff!').'"}]}';
                die();
            }
            try { $Shader = $Shader->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Shader->save()) {
                echo '{"success_msgs": [{"title": "'.Translation::translate('Saved!').'", "message": "'.Translation::translate('Saved successfully!').'"}]}';
                die();
            }
            break;
        default:
            raise404();
            die();
    }
    throw new ValidationError(array());
} catch (ValidationError $e) { echo $e->stringify(); die(); }
