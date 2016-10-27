<?php

require_once '../vendor/autoload.php';

use Renatoaraujo\Debug;

$arrExample = [
    'Example' => 'Content',
    'ExampleSubArray' => [
        'ExampleChild1' => 1,
        'ExampleChild2' => 2,
        'ExampleChild3' => 3,
    ],
];

$strExample = 'Example as string';

$intExample = 12837124;

Debug::dump($arrExample, $strExample, $intExample);