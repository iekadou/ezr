<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You have to be logged in to save!').'"}]}';
        die();
    }

    $Snippet = new Snippet();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Snippet = $Snippet->get_by(array(array("id", "=", $id)));
            if ($Snippet == false) {
                raise404();
                die();
            }
            if ($Snippet->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only save your own stuff!').'"}]}';
                die();
            }
            try { $Snippet = $Snippet->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Snippet->save()) {
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
