<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

try {
    if (Account::is_logged_in() != true) {
        echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You have to be logged in to save!').'"}]}';
        die();
    }

    $RenderPass = new RenderPass();

    if (!empty($errors)) {
        throw new ValidationError($errors);
    }
    switch (REQUEST_METHOD){
        case "PUT":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $RenderPass = $RenderPass->get_by(array(array("id", "=", $id)));
            if ($RenderPass == false) {
                raise404();
                die();
            }
            if ($RenderPass->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only save your own stuff!').'"}]}';
                die();
            }
            try { $RenderPass = $RenderPass->interpret_request($_POST, $_FILES); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            if ($RenderPass->save()) {
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
            $RenderPass = $RenderPass->set_userid(Account::get_user_id());
            try { $RenderPass = $RenderPass->set_program_id($Program->get_id()); } catch (ValidationError $e) { echo $e->stringify(); die(); }
            $RenderPass->set_texture_name('tDiffuse')->get_shader()->set_userid(Account::get_user_id())->sample_material()->save();
            if ($RenderPass->create()) {
                new View('', '', '_include/_render_pass.html');
                View::set_template_var('renderPass', $RenderPass);
                echo '{"id": "'.$RenderPass->get_id().'", "vertex_id": "'.$RenderPass->get_shader()->get_vertex_id().'", "fragment_id": "'.$RenderPass->get_shader()->get_fragment_id().'", "fragment_shader": '.json_encode(utf8_encode($RenderPass->get_shader()->get_fragment()->get_code())).', "vertex_shader": '.json_encode(utf8_encode($RenderPass->get_shader()->get_vertex()->get_code())).', "rendered_html": '.json_encode(utf8_encode(View::render($display=false))).'}';
                die();
            }
            break;
        case "DELETE":
            $id = (isset($_POST['id']) ? htmlspecialchars($_POST['id']) : false);
            $RenderPass = $RenderPass->get_by(array(array("id", "=", $id)));
            if ($RenderPass == false) {
                raise404();
                die();
            }
            if ($RenderPass->get_userid() != Account::get_user_id()) {
                echo '{"error_msgs": [{"title": "'.Translation::translate('No access!').'", "message": "'.Translation::translate('You can only delete your own stuff!').'"}]}';
                die();
            }
            if ($RenderPass->delete()) {
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
