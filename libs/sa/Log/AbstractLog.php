<?

namespace SA\Log;

/*
 * Module for AbstractLog class
 */

abstract class AbstractLog {
	/**
	 * Time template
	 * 
	 * @var string
	 */
	private $timeFormat = 'H:i:s.u';

	/**
	 * Date template
	 * 
	 * @var string
	 */
	private $dateFormat = 'd.m.Y';
	
	/**
	 * Divider
	 * 
	 * @var string
	 */
	protected $delimiter = "\t";
	
	/**
	 * Method for conevrtigns args to strings
	 *
	 * @param array $args arguments
	 * @return string
	 */
	protected function convertArgs($args) {
		$res = array();
		foreach($args as $argument) {
			switch (gettype($argument)) {
				case 'array':
					$str = array();
					foreach($argument as $key=>$value) {
						if (is_array($value)) {
							$str[] = $key.'="'.  json_encode($value).'"';
						} else {
							$str[] = $key.'='.strval($value);
						}
					}
					$res[] = implode(',', $str);
					break;
				case 'object':
					$res[] = json_encode($argument);
					break;
				default:
					$res[] = strval($argument);
			}
		}
		return $res;
	}
	
	/**
	 * Write string to log file
	 *
	 * @param ... any count of paramaters, they all whil be added in log using divider
	 * @return boolean|string return text error if error, and true if ok
	 */
	public function put() {
		if (func_num_args() == 0) {
			return 'Error: not enought paramaeters';
		}
		// create datetime with microseconds
		list($microseconds, $seconds) = explode(' ', microtime(false));
		$microseconds = $seconds . substr($microseconds, 1, 7);
		$curDateTime = \DateTime::createFromFormat('U.u', $microseconds);
		
		// convert args
		$putArray = $this->convertArgs(func_get_args());
		// add date and time
		array_unshift(
			$putArray,
			$curDateTime->format($this->timeFormat),
			$curDateTime->format($this->dateFormat)
		);
		$putStr = implode($this->delimiter, $putArray);
		
		$res = $this->writeString($putStr);
		
		if (is_string($res)) {
			return $res;
		}
		return true;
	}
	
	/**
	 * Add string to log file using sprintf as fitrst parameter
	 * @param @str sprintf template
	 * @param ... @args arguments for template
	 * @return boolean|string return text error if error, and true if ok
	 */
	public function pput() {
		$args = func_get_args();
		$str = array_shift($args);
		return $this->put(vsprintf($str, $args));
	}
	
	/**
	 * Mathod add separator to log file
	 * Looks like $template($length/2) $text $template($length/2) 
	 * Example
	 * ---------------------- Sample text ----------------------
	 * 
	 * @param string $text text in the middle
	 * @param type $length repeat string multiplier
	 * @param string $template template for repeat string
	 * @return boolean|string return text error if error, and true if ok
	 */
	public function addDelimiter($text = '', $length = 50, $template = '-') {					
		$newLength = ceil(($length - strlen($text)) / 2);
		if ($newLength < 0) {
			$newLength = 0;
		}
		$repeater = str_repeat($template, $newLength);
		return $this->pput('%s %s %s', $repeater, $text, $repeater);
	}
	
	/**
	 * Method should be overwrited,
	 * 
	 * @param strign $logStr string to add to log
	 * @return boolean|string return text error or true 
	 */
	abstract protected function writeString($logStr);
}
