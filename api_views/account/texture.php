<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You have to be logged in to save!').'"}]}';
        die();
    }

    $Texture = new Texture();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Texture = $Texture->get_by(array(array("id", "=", $id)));
            if ($Texture == false) {
                raise404();
                die();
            }
            if ($Texture->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only save your own stuff!').'"}]}';
                die();
            }
            try { $Texture = $Texture->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Texture->save()) {
                echo '{"success_msgs": [{"title": "'.Translation::translate('Saved!').'", "message": "'.Translation::translate('Saved successfully!').'"}], "id": "'.$Texture->get_id().'", "name": "'.$Texture->get_name().'", "img": "/'.$Texture->get_img().'"}';
                die();
            }
            break;
        case "POST":
            if (Account::get_user()->is_activated() != true) {
                raise404();
                die();
            }
            $Program = new Program();
            $Program = $Program->get_by(array(array("id", "=", $_POST['program_id']), array("userid", "=", Account::get_user_id())));
            if ($Program == false) {
                echo '{"error_msgs": [{"title": "' . Translation::translate('No access!') . '", "message": "' . Translation::translate('You can only add stuff to your own stuff!') . '"}]}';
                die();
            }
            $Texture = $Texture->set_userid(Account::get_user_id());
            try { $Texture = $Texture->set_program_id($Program->get_id())->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($Texture->create()) {
                new View('', '', '_include/_texture.html');
                View::set_template_var('texture', $Texture);
                echo '{"id": "'.$Texture->get_id().'", "name": "'.$Texture->get_name().'", "img": "/'.$Texture->get_img().'", "rendered_html": '.json_encode(utf8_encode(View::render($display=false))).'}';
                die();
            }
            break;
        case "DELETE":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $Texture = $Texture->get_by(array(array("id", "=", $id)));
            if ($Texture == false) {
                raise404();
                die();
            }
            if ($Texture->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only delete your own stuff!').'"}]}';
                die();
            }
            if ($Texture->delete()) {
                echo '{"success_msgs": [{"title": "'.Translation::translate('Deleted!').'", "message": "'.Translation::translate('You deleted this shader pass successfully!').'"}]}';
                die();
            } else {
                echo '{"error_msgs": [{"title": "'.Translation::translate('Error!').'", "message": "'.Translation::translate('Could not delete shader pass!').'"}]}';
                die();
            }
            break;
        default:
            raise404();
            die();
    }
    throw new ValidationError(array());
} catch (ValidationError $e) { echo $e->stringify(); die(); }
