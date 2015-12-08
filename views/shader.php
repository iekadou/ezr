<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");


$VertexShader = new Shader();
$vertex_shader_id = (isset($_GET['vertex_shader_id']) ? htmlspecialchars($_GET['vertex_shader_id']) : false);
$VertexShader = $VertexShader->get_by(array(array("id", "=", $vertex_shader_id), array("type", "=", Shader::$ShaderTypes['vertex'])));

if ($VertexShader == false) {
    raise404();
    die();
}


$FragmentShader = new Shader();
$fragment_shader_id = (isset($_GET['fragment_shader_id']) ? htmlspecialchars($_GET['fragment_shader_id']) : false);
$FragmentShader = $FragmentShader->get_by(array(array("id", "=", $fragment_shader_id), array("type", "=", Shader::$ShaderTypes['fragment'])));

if ($FragmentShader == false) {
    raise404();
    die();
}


new View('Shader', Translation::translate('Shader'), "shader.html");
Globals::set_var('VertexShader', $VertexShader);
Globals::set_var('FragmentShader', $FragmentShader);

View::render();
