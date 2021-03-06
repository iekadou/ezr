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
            $ShaderPass = $ShaderPass->get_by(array(array("id", "=", $id)));
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
                echo '{"success_msgs": [{"title": "'.Translation::translate('Saved!').'", "message": "'.Translation::translate('Saved successfully!').'"}]}';
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
            $ShaderPass = $ShaderPass->set_userid(Account::get_user_id());
            try { $ShaderPass = $ShaderPass->set_program_id($Program->get_id()); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            $ShaderPass->get_shader()->set_userid(Account::get_user_id())->sample_post_processing()->save();
            if ($ShaderPass->create()) {
                new View('', '', '_include/_shader_pass.html');
                View::set_template_var('shaderPass', $ShaderPass);
                echo '{"id": "'.$ShaderPass->get_id().'", "vertex_id": "'.$ShaderPass->get_shader()->get_vertex_id().'", "fragment_id": "'.$ShaderPass->get_shader()->get_fragment_id().'", "fragment_shader": '.json_encode(utf8_encode($ShaderPass->get_shader()->get_fragment()->get_code())).', "vertex_shader": '.json_encode(utf8_encode($ShaderPass->get_shader()->get_vertex()->get_code())).', "rendered_html": '.json_encode(utf8_encode(View::render($display=false))).'}';
                die();
            }
            break;
        case "DELETE":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $ShaderPass = $ShaderPass->get_by(array(array("id", "=", $id)));
            if ($ShaderPass == false) {
                raise404();
                die();
            }
            if ($ShaderPass->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only delete your own stuff!').'"}]}';
                die();
            }
            if ($ShaderPass->delete()) {
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
