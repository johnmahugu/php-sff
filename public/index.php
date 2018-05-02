<?php
if (php_sapi_name() == 'cli-server') {
	// removes first slash '/' before checking 
	// (absolute to relative path conversion)
	if (file_exists(substr($_SERVER['REQUEST_URI'],1))) {
		return false;		
	}    
}
require_once('../components/Application.php');

$app = new Application();
$app->run();
