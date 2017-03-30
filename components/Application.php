<?php
/*
This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org>
*/

/**
*
* PHP Single-File-Framework
*
* PHP-SFF is a minimalistic framework that does just these things:
*  - Simplistic class auto-loading (include $className.php)
*  - Routing requests with the following format: /controller/action/param0/paramN..
*  - CSRF prevention using Origin+Host check for non-GET/HEAD requests
*  - Layout rendering
*  - REST APIs output encoding using json_encode
*
* @author Rodrigo Valceli Raimundo <rodrigovalceli@gmail.com>
*/

class Application {
	// disable only for REST / APIs that use token authentication on every request
	public $csrfProtection = true;
	// default route (keep leading slash)
	public $defaultRoute = '/site/index';
	// error route
	public $errorRoute = '/site/error';
	// default layout file (can be changed per request)
	public $layout = 'layout';

	public function __construct() {
		spl_autoload_register(function ($class_name) {
			// refuses to load any weird class name
			if (preg_match('/^[0-1_a-zA-Z]+$/', $class_name)) {
				include $class_name . '.php';
			}
		});
	}

	public function run() {
		// CSRF check
		if ($this->csrfProtection &&
		 !in_array($_SERVER['REQUEST_METHOD'], [ 'GET', 'HEAD' ])) {
			$origin = filter_input(INPUT_SERVER, 'HTTP_ORIGIN');
			$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
			$pos = strpos($origin, "://") + 3;
			if (!$origin || strpos($origin,$host,$pos) !== $pos) {
				http_response_code(403);
				die('CSRF positive check');
			}
		}
		// Route dispatch
		$path = filter_input(INPUT_SERVER, 'PATH_INFO');
		if (empty($path)) {
			$path = $this->defaultRoute;
		}
		list($call, $params) = $this->parseRoute($path); 
		if (!is_callable($call)) {
			list($call, $params) = $this->parseRoute($this->errorRoute);
			$params = [ $this, 404 ];	
		} else {
			array_unshift($params, $this);
		}
		$result = call_user_func_array($call, $params);
		if (isset($result[0])) {
			http_response_code($result[0]);
		}
		if (isset($result[1])) {
			header('Content-Type: application/json');
			echo json_encode($result[1]);
		}
	}

	public function render($viewName, $args = array()) {
		ob_start();
		if (!empty($args)) extract($args); // populate local scope with arg
		include "../views/$viewName.php"; //
		$body = ob_get_clean();
		include "../views/{$this->layout}.php";
	}

	private function parseRoute($path) {
		$path = explode('/', $path);
		// first element is always empty
		array_shift($path);
		// build the ClassName
		$c = array_shift($path);
		$class_name = ucfirst($c) . 'Controller';
		// build the methodName
		$c = array_shift($path);
		$method_name = strtolower($_SERVER['REQUEST_METHOD']) .
						( $c ? ucfirst($c) : 'Index' );
		return  [ [ new $class_name, $method_name ], $path ];
	}
}
