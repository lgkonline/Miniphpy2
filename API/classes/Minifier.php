<?php

class Minifier {
	private $urlJS = "http://javascript-minifier.com/raw";
	private $urlCSS = "http://cssminifier.com/raw";
	
	public function minify($format, $content, $compression_option = "remote") {
		if ($format == "js") {
			if ($compression_option == "remote") {
				return $this->getMinified($this->urlJS, $content); // remote compression
			}
			else {
				require "vendor/JShrink/Minifier.php";
				return \JShrink\Minifier::minify($content); // local compression
			}
		}
		if ($format == "css") {
			if ($compression_option == "remote") {
				return $this->getMinified($this->urlCSS, $content);	 // remote compression
			}
			else {
				require "vendor/cssmin/cssmin-v3.0.1-minified.php";
				return CssMin::minify($content);	// local compression
			}
		}
	}
	
	private function getMinified($url, $content) {
		$postdata = array('http' => array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/x-www-form-urlencoded',
	        'content' => http_build_query( array('input' => $content) ) ) );
		return file_get_contents($url, false, stream_context_create($postdata));
	}	
    
    public static function minifyAndSave($format, $dom, $input, $minifier) {
        // $format = "css" | "js"
        
        $status_code = 200;    
        
        $formatArea = $dom->getElementById("miniphpy-" . $format);
        $formatInput = "";
        
        $attr = $format == "css" ? "href" : "src";
        $outputFile = $format == "css" ? $input->outputFileCss : $input->outputFileJs;
        
        foreach($formatArea->childNodes as $currLinkElement) {
            $currInput = $currLinkElement->getAttribute($attr);
            
            if ($currInput != "") {
                // Is relative path, no URL and root path is set
                if (isset($input->path) && $input->path != "" && LittleHelpers::isAbsolutePath($currInput) == false && LittleHelpers::isValidUrl($currInput) == false) {
                    $currInput = $input->path . DIRECTORY_SEPARATOR . $currInput;
                }
                
                // Überprüft ob URL oder Datei existiert
                if (LittleHelpers::isValidUrl($currInput) || file_exists($currInput)) {
                    $file = file_get_contents($currInput);
                    $formatInput .= $file . "\n";
                }
                // Maybe user missed to set the protocol to url
                elseif (LittleHelpers::isValidUrl("http:" . $currInput)) {
                    $file = file_get_contents("http:" . $currInput);
                    $formatInput .= $file . "\n";
                }
                else {
                    // Datei nicht gefunden
                    $status_code = 400;
                }
            }        
        }
        
        if ($status_code == 200) {
            $output = $minifier->minify($format, $formatInput, "local");
            
            $output_file = fopen($outputFile, "w") or die("Unable to open '$outputFile'.");
            fwrite($output_file, $output);
            fclose($output_file);		
        }
        
        return $status_code;
    }
}