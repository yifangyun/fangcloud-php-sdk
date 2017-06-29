<?php
require __DIR__ . '/Burgomaster.php';

// Creating staging directory at guzzlehttp/src/build/artifacts/staging.
$stageDirectory = __DIR__ . '/artifacts/staging';
// The root of the project is up one directory from the current directory.
$projectRoot = __DIR__ . '/../';
$packager = new \Burgomaster($stageDirectory, $projectRoot);

// Copy basic files to the stage directory. Note that we have chdir'd onto
// the $projectRoot directory, so use relative paths.
foreach (['README.md', 'CHANGELOG.md'] as $file) {
    $packager->deepCopy($file, $file);
}

// Copy each dependency to the staging directory. Copy *.php and *.pem files.
$packager->recursiveCopy('src', 'Fangcloud', ['php', 'pem']);
$packager->recursiveCopy('vendor/guzzlehttp/guzzle/src', 'GuzzleHttp');

// Create the classmap autoloader, and instruct the autoloader to
// automatically require the 'GuzzleHttp/functions.php' script.
$packager->createAutoloader([], 'fangcloud-autoload.php');

// Create a phar file from the staging directory at a specific location
$packager->createPhar(__DIR__ . '/artifacts/fangcloud-php-sdk.phar');

// Create a zip file from the staging directory at a specific location
$packager->createZip(__DIR__ . '/artifacts/fangcloud-php-sdk.zip');