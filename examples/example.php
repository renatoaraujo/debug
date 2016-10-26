<?php

require_once '../vendor/autoload.php';

use Renatoaraujo\Debug;

$arrSomeArray = [
    'Hello' => 'World',
];
$strSomeString = 'Hello World';


Debug::dump($arrSomeArray, 1, 'exit');

//echo 'audh';

//echo Debug::printr($arrSomeArray);
//echo Debug::json($arrSomeArray);
//echo Debug::console($arrSomeArray);
Debug::$strLogDateTime = 'asd';
//echo Debug::log($arrSomeArray, $strSomeString);