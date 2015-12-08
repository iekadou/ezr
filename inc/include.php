<?php
namespace Iekadou\Webapp;

$RENDERING_START = microtime(true);
// get request method
if (isset($_POST['_method']) && ($_POST['_method'] == 'GET' || $_POST['_method'] == 'POST' || $_POST['_method'] == 'PUT' || $_POST['_method'] == 'DELETE')) {
    define('REQUEST_METHOD',  $_POST['_method']);
} else {
    define('REQUEST_METHOD', "GET");
}

session_start();
define('PATH', $_SERVER['DOCUMENT_ROOT'].'/');
define('Lare', true);

// configs
include(PATH."config/webapp.php");

// includes
require_once(PATH."vendor/autoload.php");
require_once(PATH."classes/Globals.php");
include(PATH."inc/utils.php");
require_once(PATH."classes/Translation.php");
require_once(PATH."classes/View.php");
require_once(PATH."classes/UrlsPy.php");
require_once(PATH."classes/Pagination.php");
require_once(PATH."classes/TwigUrl.php");
require_once(PATH."classes/TwigTrans.php");
require_once(PATH."classes/TwigTime.php");
require_once(PATH."classes/View.php");

require_once(PATH."classes/DBConnector.php");
require_once(PATH."classes/BaseModel.php");
require_once(PATH."classes/User.php");
require_once(PATH."classes/Shader.php");
require_once(PATH."classes/Files.php");
require_once(PATH."classes/ValidationError.php");
require_once(PATH."classes/Migration.php");

require_once(PATH."classes/Account.php");
Globals::set_var('Account', new Account());


Globals::set_var('SITE_NAME', SITE_NAME);
Globals::set_var('DOMAIN', DOMAIN);

$INIT_LOADED = true;
