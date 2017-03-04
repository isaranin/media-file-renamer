<?

/* 
 * Module autoload
 */
spl_autoload_register(function ($className) {
	$namespaces = explode('\\', $className);

	if ((count($namespaces) === 0) || (strtolower($namespaces[0]) !== 'mfr')) {
		return false;
	}

	// remove namespace
	array_shift($namespaces);

	$className = implode('/', $namespaces);
	// if need add exception class
	if ( (stristr($className, "exception") !== false) ) {
		$path = substr($className,0,strrpos($className,'/'));
		$path = __DIR__ . '/' . $path . '/Exceptions.class.php';
		if ( file_exists($path) ) {
			require_once($path);
			return true;
		}
	}

	$path = __DIR__.'/'.$className.'.class.php';
	if (file_exists($path)) {
		require_once($path);
		return true;
	}

	$path = __DIR__.'/'.$className.'.php';
	if (file_exists($path)) {
		require_once($path);
		return true;
	}

	return false;
});


