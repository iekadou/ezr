<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        raise404();
        die();
    }

    $Program = new Program();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Program = $Program->get_by(array(array("id", "=", $id)));
            try { $Program = $Program->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Program == false) {
                raise404();
                die();
            }
            if ($Program->save()) {
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
