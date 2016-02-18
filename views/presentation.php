<?php
namespace Iekadou\Webapp;
use Lare_Team\Lare\Lare as Lare;
require_once("../inc/include.php");

$Program = new Program();
if ($_GET['presentation_slug'] == 'thankyou') {
    $Program = $Program->get(38);
} else {
    $Program = $Program->get(1);
}

if ($Program == false) {
    raise404();
    die();
}

new View('Presentation', Translation::translate('Presentation'), "presentation.html");
Globals::set_var('Program', $Program);
Globals::set_var('site', $_GET['presentation_slug']);

if ($_GET['presentation_slug'] == 'thankyou') {
    Lare::set_current_namespace("NO LARE PLEASE!");
    View::set_template_var('LARE_PREFIX', "no");
    View::set_template_var('lare_current_namespace', "NO LARE PLEASE!");
    View::set_template_var('lare_matching', 0);
}

View::render();
