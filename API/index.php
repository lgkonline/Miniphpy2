<?php

header("Content-type: text/json");

$isApi = true;
require "../index.php";

require "classes/Config.php";
require "classes/Input.php";
require "classes/LittleHelpers.php";
require "classes/Minifier.php";

$action_get = filter_input(INPUT_GET, "action");
$action_post = filter_input(INPUT_POST, "action");

$config = new Config();

if ($action_get == "config") {
    
    echo json_encode($config);
}


if ($action_get == "set-config") {
    $receivedConfig = filter_input(INPUT_GET, "receivedConfig");

    $config->updateConfigFile($receivedConfig);
    
    echo json_encode(array(
        "response" => "OKAY"
    ));
    exit;
}

if ($action_get == "minify") {
    try {
        $inputID = filter_input(INPUT_GET, "inputID");
        
        if ($inputID) {
            $input = LittleHelpers::getObjectFromArray($config->inputs, "inputID", $inputID);
            
            $inputFile = $input->inputFile;
            $htmlString = file_get_contents($inputFile);
            
            // deaktiviert das Anzeigen von Warnungen
            error_reporting(E_ERROR | E_PARSE);
            
            $dom = new DOMDocument();
            $dom->loadHTML($htmlString);
            
            $minifier = new Minifier();
            
            $codes = [
                Minifier::minifyAndSave("css", $dom, $input, $minifier),
                Minifier::minifyAndSave("js", $dom, $input, $minifier)
            ];
            
            $status_code = max($codes);
            
            http_response_code($status_code);
            
            echo json_encode(array(
                "CSS" => $codes[0],
                "JS" => $codes[1]
            ));
        }  
    }
    catch (Exception $ex) {
        echo 'Exception abgefangen: ', $ex->getMessage(), "\n"; 
    }
    exit;
}