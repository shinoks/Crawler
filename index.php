<?php
require "vendor/autoload.php";
            
function classLoader($classname){
    $file = 'controller/'.$classname.'.php';
    if (file_exists($file) && is_readable($file) && !class_exists($classname, false)){
        require_once($file);
    }else{
        throw new Exception('Class cannot be found ( ' . $classname . ' )');
    }
}
spl_autoload_register('classLoader');
            
// Start session
session_start();
if(!isset($_SESSION['uzytkownik']))
{
// Sesja się zaczyna, wiec inicjujemy użytkownika anonimowego
    $_SESSION['uzytkownik'] = 0;
}

// Include Composer Autoload (relative to project root).
require_once "vendor/autoload.php";

require_once dirname(__FILE__).'/vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$twig = new Twig_Environment( new Twig_Loader_Filesystem("./view"),
    array( "cache" => "./view/cache" ) );

    include_once ('route_start.php');

