<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");


$VertexShader = new Shader();
$vertexShaders = $VertexShader->filter_by(array(array("type", "=", Shader::$ShaderTypes['vertex'])));


$FragmentShader = new Shader();
$fragmentShaders = $FragmentShader->filter_by(array(array("type", "=", Shader::$ShaderTypes['fragment'])));

new View('Shader Selection', Translation::translate('Shader Selection'), "shader_selection.html");
Globals::set_var('vertexShaders', $vertexShaders);
Globals::set_var('fragmentShaders', $fragmentShaders);

View::render();


