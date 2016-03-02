<?php

class Minifier {
	private $urlJS = "https://javascript-minifier.com/raw";
	private $urlCSS = "https://cssminifier.com/raw";
	
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
        
        if ($formatArea != "") {
            // check if there is a root path given in the HTML document
            $metaElements = $dom->getElementsByTagName("meta");
            // $miniphpyRootPath = dirname($input->inputFile) . DIRECTORY_SEPARATOR;
            $miniphpyRootPath = "";
            
            foreach ($metaElements as $currMeta) {
                if ($currMeta->getAttribute("name") == "miniphpy-root") {
                    // is miniphpy-root meta tag
                    $miniphpyRootPath = $currMeta->getAttribute("content");
                
                    $lastChar = substr($miniphpyRootPath, -1);
                    
                    if (!($lastChar == "/" || $lastChar == "\\")) {
                        $miniphpyRootPath .= DIRECTORY_SEPARATOR;
                    }
                }
            }
            
            $formatInput = "";
            $outputAttr = $formatArea->getAttribute("data-miniphpy-output");
            
            if (isset($outputAttr) && $outputAttr != "") {
                // output file is set in HTML document
                $outputFile = self::formatPath($miniphpyRootPath, $outputAttr);
            }
            else {
                // default output file
                $outputFile = $miniphpyRootPath . "minified." . $format;
            }
            
            $attr = $format == "css" ? "href" : "src";
            
            foreach($formatArea->childNodes as $currLinkElement) {
                $currInput = $currLinkElement->getAttribute($attr);
                
                if ($currInput != "") {
                    $currInput = self::formatPath($miniphpyRootPath, $currInput);
                    
                    // Überprüft ob URL oder Datei existiert
                    if (LittleHelpers::isValidUrl($currInput) || file_exists($currInput)) {
                        $file = file_get_contents($currInput);
                        $formatInput .= $file . " ";
                    }
                    // Maybe user missed to set the protocol to url
                    elseif (LittleHelpers::isValidUrl("http:" . $currInput)) {
                        $file = file_get_contents("http:" . $currInput);
                        $formatInput .= $file . " ";
                    }
                    else {
                        print_r($currLinkElement->getAttribute($attr));
                        echo "<br>";
                        print_r($currInput);
                        exit;
                        
                        // Datei nicht gefunden
                        $status_code = 400;
                    }
                }        
            }
            
            if ($status_code == 200) {
                if (DEBUG_MODE) {
                    $compress_option = "local";
                }
                else {
                    $compress_option = "remote";
                }
                
                $output = $minifier->minify($format, $formatInput, $compress_option);
                
                $output_file = fopen($outputFile, "w") or die("Unable to open '$outputFile'.");
                fwrite($output_file, $output);
                fclose($output_file);		
            }
            
        }
        else {
            $status_code = 100;
        }
        return $status_code;
    }
    
    public static function formatPath($miniphpyRootPath, $path) {
        $currInput = str_replace("~/", "./", $path);
        
        // Insert root path to input path if it is given
        if (isset($miniphpyRootPath)) {
            $currInput = str_replace("./", $miniphpyRootPath, $currInput);
        }   
                
        // Is relative path, no URL and root path is set
        if (isset($miniphpyRootPath) && $miniphpyRootPath != "" && LittleHelpers::isAbsolutePath($currInput) == false && LittleHelpers::isValidUrl($currInput) == false) {
            $currInput = $miniphpyRootPath . DIRECTORY_SEPARATOR . $currInput;
        }
        
        return $currInput;     
    }
}