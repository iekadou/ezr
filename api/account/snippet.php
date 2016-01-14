<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        raise404();
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
            try { $Snippet = $Snippet->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Snippet == false) {
                raise404();
                die();
            }
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
