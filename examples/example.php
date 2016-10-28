<?php

require_once '../vendor/autoload.php';

use Renatoaraujo\Debug;

$arrExample = (object) [
    'Example' => 'Content',
    'ExampleSubArray' => [
        'ExampleChild1' => 'Example String',
        'ExampleChild2' => 1.75,
        'ExampleChild3' => 3,
    ],
];

$strExample = 'Example as string';

$intExample = 12837124;

Debug::dump($arrExample, $strExample, $intExample);