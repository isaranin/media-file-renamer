<?

namespace SA\Log;

/*
 * Module for ClassWithLog class
 */

class ClassWithLog {
	/**
	 * Log object
	 * @var AbstractLog
	 */
	public $log = null;
	
	/**
	 * Put string to log object, params as in AbstractLog->put function
	 */
	public function put() {
		if (!is_null($this->log)) {
			call_user_func_array([$this->log, 'put'], func_get_args());
		}
	}
	
	/**
	 * Put string to log object, params as in AbstractLog->pput function
	 */
	public function pput() {
		if (!is_null($this->log)) {
			call_user_func_array([$this->log, 'pput'], func_get_args());
		}
	}
}
