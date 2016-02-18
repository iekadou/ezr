<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

$Program = new Program();
$Program = $Program->get(1);

if ($Program == false) {
    raise404();
    die();
}

new View('Presentation', Translation::translate('Presentation'), "presentation.html");
Globals::set_var('Program', $Program);
Globals::set_var('site', $_GET['presentation_slug']);

View::render();
