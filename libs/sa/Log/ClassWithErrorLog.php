<?

namespace SA\Log;

/*
 * Module for ClassWithErrorLog class
 */

class ClassWithErrorLog extends ClassWithLog{
	/**
	 * Error log object
	 * @var AbstractLog
	 */
	public $errorLog = null;
	
	/**
	 * Put string to log object, params as in AbstractLog->put function
	 */
	public function eput() {
		$params = func_get_args();
		if (!is_null($this->errorLog)) {
			call_user_func_array([$this->errorLog, 'put'], $params);
		}
		if (!is_null($this->log)) {
			array_unshift($params, 'Error: ');
			call_user_func_array([$this->log, 'put'], $params);
		}
	}
	
	/**
	 * Put string to log object, params as in AbstractLog->pput function
	 */
	public function epput() {
		$params = func_get_args();
		if (!is_null($this->errorLog)) {
			call_user_func_array([$this->errorLog, 'pput'], $params);
		}
		if (!is_null($this->log)) {
			$params[0] = 'Error: '.$params[0];
			call_user_func_array([$this->log, 'pput'], $params);
		}
	}
}
