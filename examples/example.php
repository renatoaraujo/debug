<?php

require_once '../vendor/autoload.php';

use Renatoaraujo\Debug;

$objExample = (object) [
    'Example' => 'Content',
    'ExampleSubArray' => [
        'ExampleChild1' => 'String sample',
        'ExampleChild2' => 1.75,
        'ExampleChild3' => 3,
    ],
    'ExampleClosure' => function () {
        return function () {
            $closure = true;
            return $closure;
        };
    },
];

$strExample = 'Example as string';
$booExample = false;

$intExample = 12837124;
Debug::$isPretty = true;
Debug::console($objExample, $strExample, $intExample, $booExample);
