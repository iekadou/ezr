<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");

new View('Imprint', Translation::translate('Imprint'), "imprint.html");
View::render();
