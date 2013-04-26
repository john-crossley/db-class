<?php
define('ROOT', str_replace('\\', '/', dirname(__FILE__)) . '/');
$path1 = explode('/', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME'])));
$path2 = explode('/', substr(ROOT, 0, -1));
$path3 = explode('/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])));
for ($i = count($path2); $i < count($path1); $i++) array_pop($path3);
$url = $_SERVER['HTTP_HOST'] . implode('/', $path3);
($url{strlen($url) -1} == '/') ? define('URL', 'http://' . $url) : define('URL', 'http://' . $url . '/');
define('APP', ROOT . 'app/');
define('CONFIG', APP . 'config/');
define('HELPERS', APP . 'helpers/');


set_include_path(implode(PATH_SEPARATOR, array(
  ROOT . 'app/library',
  get_include_path()
)));


function __autoload($className) {
  require_once strtolower($className) . '.php';
}

require_once CONFIG . 'config.php';
require_once HELPERS . 'functions.php';
