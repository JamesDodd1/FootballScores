<?php
// 
// Website HTML directory
// 
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/public_html');
//define('ROOT', dirname(__FILE__) . '/../public_html');
// 
// Website Styling
// 
define('STYLE', '/resources/css');
// 
// Website Repeating Code
// 
define('RESOURCES', $_SERVER['DOCUMENT_ROOT'] . '/resources/includes');
// 
// Database connection details
// 
define('DATABASE_CONFIG', dirname(__FILE__) . '/database.php');
// 
// Images
// 
define('IMAGES', '/resources/img');

?>