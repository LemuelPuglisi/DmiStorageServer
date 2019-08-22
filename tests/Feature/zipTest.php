<?php

    $zip = new ZipArchive; 
    $name = 'file.zip'; 
    $response = $zip->open($name, ZipArchive::CREATE);

    if ($response === TRUE) {
        $zip->addFile('file.pdf'); 
    }

    $zip->close(); 
