<?php
namespace Iekadou\Webapp;
require_once("../../inc/include.php");

$Shader = new Shader();
$id = (isset($_GET['id']) ? htmlspecialchars($_GET['id']) : false);
if ($id != false) {
    $Shader = $Shader->get_by(array(array("id", "=", $id), array("userid", "=", Account::get_user_id())));
    Globals::set_var('form_method', 'PUT');
} else { 
    Globals::set_var('form_method', 'POST');
}
Globals::set_var('Shader', $Shader);
Globals::set_var('ShaderTypes', $Shader::$ShaderTypes);
if (Account::is_logged_in() != true || !$Shader) {
    raise404();
    die();
}

new View('Shader', Translation::translate('Shader'), 'account/shader.html');
Globals::set_var('dashboard_active', true);
View::render();
