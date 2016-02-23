<?php

class Config {
    public $configFile = "../config.json";
    public $configVersion;
    public $inputs = [];
    
    public function Config() {
        $configJson = file_get_contents($this->configFile);
        $configObj = json_decode($configJson);      
        
        $this->configVersion = $configObj->configVersion;
        $this->initInputs($configObj->inputs); 
    }
    
    private function initInputs($inputsFromJson) {
        foreach ($inputsFromJson as $currInputFromJson) {
            array_push($this->inputs, new Input($currInputFromJson));
        }
    }
    
    public function updateConfigFile($configJson) {
        if (isset($configJson)) {
            $output = $configJson;
        }
        else {
            $output = json_encode($config);
        }
        
        $output_file = fopen($this->configFile, "w") or die("Unable to open '$this->configFile'.");
        fwrite($output_file, $output);
        fclose($output_file);
    } 
}