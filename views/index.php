<?php
namespace Iekadou\Webapp;
require_once("../inc/include.php");
new View('Home', Translation::translate('Home'), 'index.html');
View::render();
