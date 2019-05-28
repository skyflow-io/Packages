<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$fileSystem = new Filesystem();
$result = [];
$docResult = [];
$composeFinder = new Finder();
$composeFinder->directories()->depth(0)->in('/app/src')->sortByName();

foreach ($composeFinder as $compose) {
    $name = $compose->getBasename();
    $files = [
        'name' => $name,
        'files' => []
    ];
    $filesFinder = new Finder();
    $filesFinder->files()->in('/app/src/' . $name);

    foreach ($filesFinder as $file) {
        $filename = $file->getFilename();
        $files['files'][] = [
            'directory' => $file->getRelativePath(),
            'filename' => $filename,
            'contents' => $file->getContents(),
        ];
        if($filename === ($name . '.config.json')){
            $doc = json_decode($file->getContents(), true);
            $docResult[] = $doc;
        }
    }

    $result[] = $files;
}

$fileSystem->remove(['/app/data/packages.json', '/app/doc/packages.json']);
$fileSystem->dumpFile('/app/data/packages.json', json_encode($result));
$fileSystem->dumpFile('/app/doc/packages.json', json_encode($docResult));