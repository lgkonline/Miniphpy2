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
}