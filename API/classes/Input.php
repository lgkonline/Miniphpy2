<?php

class Input {
    public $inputID;
    public $inputFile;
    public $title;
    
    public $path;
    
    public function Input($objFromJson = false) {
        if ($objFromJson != false) {
            $this->set($objFromJson);
            
            $this->path = dirname($this->inputFile);
        }
    }
    
    private function set($data) {
        foreach ($data AS $key => $value) {
            if (is_array($value)) {
                $sub = new JSONObject;
                $sub->set($value);
                $value = $sub;
            }
            $this->{$key} = $value;
        }
    }    
}