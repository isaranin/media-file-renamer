<?

namespace SA\Log;

/*
 * Module for File class
 */

class File extends AbstractLog{
	
	/**
	 * Filename, you can use time format variables
	 * @see http://php.net/manual/en/function.strftime.php
	 *
	 * @var string
	 */
	private $fileName;
	
	/**
	 * Max size for log file
	 * 
	 * @var int
	 */
	private $maxSize = -1;

	/**
	 * Open log filename
	 *
	 * @param string $fileName file to open
	 * @return false|descriptor open file desctipor or false if error
	 */
	protected function openFile($fileName) {
		$dirName = dirname($fileName);
		// check if folder exist, if not create it
		if (!file_exists($dirName)) {
			mkdir($dirName, 0777, true);
		}
		$openMode = 'a';

		// clear cach
		clearstatcache();

		// if file more bigger then we need then replace file
		if (file_exists($fileName) &&
			($this->maxSize > 0) &&
			(filesize($fileName) >$this->maxSize)) {
			$openMode = 'w';
		}

		// open file
		return fopen($fileName, $openMode);
	}

	
	protected function writeString($logStr) {
		if (empty($this->fileName)) {
			return false;
		}		
		$fileName = strftime($this->fileName, time());
		// open file
		$fp = $this->openFile($fileName);
		if ($fp === false) {
			return sprintf('Error can`t open file: %s', $fileName);
		}
		
		$res = fwrite($fp, $logStr.PHP_EOL);
		fclose($fp);
		if (!$res) {
			return sprintf('Error with writing to log file: %s', $fileName);
		}
		
		return true;
	}
		
	/**
	 * Constructor
	 *
	 * @param string $fileName log filename 
	 * @see http://php.net/manual/en/function.strftime.php
	 * @param integer $maxSize max filesize, -1 means dont use
	 * @param string $delimiter divider
	 */
	public function __construct($fileName, $maxSize = -1, $delimiter="\t") {
		$this->fileName = $fileName;
		$this->maxSize = $maxSize;
		$this->delimiter = $delimiter;
	}
}

