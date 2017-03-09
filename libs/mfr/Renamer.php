<?

/*
 * Media file renamer
 * https://github.com/isaranin/media-file-renamer
 *  
 * Rename media files photos/video/mp3 by it tags
 *  
 * repository		git@github.com:isaranin/media-file-renamer.git
 *  
 * author		Ivan Saranin <ivan@saranin.com>
 * company		saranin.com
 * url			http://www.saranin.com
 * copyright		(c) 2017, saranin.com
 *  
 *  
 * created by Ivan Saranin <ivan@saranin.com>, on 04-Mar-2017, at 23:07:29
 */

namespace MFR;

/*
 * Module for Renamer class
 */

class Renamer extends \SA\Log\ClassWithErrorLog{
	
	protected $template;
	
	protected $inFolder;
	
	protected $outFolder;
	
	protected $getID3;
	
	protected $nameGenerator;
	
	protected $duplicateFolder = '';
	
	protected $wrongTemplateFolder = '';
	
	private $replacementFile = '';
	
	public function __construct($aDuplicateFolder, $aWrongTemplateFolder, $replacementFile = '') {
		$this->getID3 = new \getID3();
		$this->nameGenerator = new File\NameGenerator();
		$this->replacementFile = $replacementFile;
		$repl = [];
		if (file_exists($replacementFile)) {
			$repl = json_decode(file_get_contents($replacementFile), true);
		}
		$this->nameGenerator->replacement = $repl;
		$this->duplicateFolder = $aDuplicateFolder;
		$this->wrongTemplateFolder = $aWrongTemplateFolder;
	}
	
	public function __destruct() {
		if (!empty($this->replacementFile)) {
			file_put_contents($this->replacementFile, 
					json_encode($this->nameGenerator->replacement, JSON_PRETTY_PRINT));
		}
	}
	
	protected function moveFile($aSrc, $aDst) {
		if (!file_exists(dirname($aDst))) {
			mkdir(dirname($aDst), 0775, true);
		}
		$res = rename($aSrc, $aDst);
		if (!$res) {
			$this->epput('Can`t move file from "%s" to "%s"', $aSrc, $aDst);
		}
		return $res;
	}
	
	protected function isFileSame($aSrc, $aDst) {
		return md5_file($aSrc) === md5_file($aDst);
	}
	
	protected function findNotExistIndex($aFilename) {
		$i = 1;
		$pathInfo = pathinfo($aFilename);
		do {
			$newFilename = sprintf('%s/%s_%\'.02d.%s',
					$pathInfo['dirname'], 
					$pathInfo['filename'], 
					$i,
					$pathInfo['extension']);
			$i++;
		} while (file_exists($newFilename));
		return $newFilename;
	}
	
	protected function renameFilesInFolder($aFolder) {
		
		$curFolder = $this->inFolder.'/'.$aFolder;
		if (!file_exists($curFolder)) {
			$this->epput('Directory "%s" not found ', $curFolder);
			return false;
		}
		
		$this->pput('Scanning directory: %s', $aFolder);
		
		$files = scandir($curFolder);
		array_shift($files);
		array_shift($files);
		$this->pput('Finded files: %d', count($files));
		foreach($files as $file) {
			$partFilename = $aFolder.'/'.$file;
			$fullFilename = $this->inFolder.$partFilename;
			if (is_dir($fullFilename)) {
				$this->pput('File "%s" is directory', $fullFilename);
				$this->renameFilesInFolder($partFilename);
				continue;
			}
			
			$this->pput('Analyzing file: %s', $fullFilename);
			$tags = $this->getID3->analyze($fullFilename);
			
			if (isset($tags['error'])) {
				$this->put('Error analyzing ', $tags['error']);
				continue;
			}
			
			$outFileName = $this->nameGenerator->makeName($this->template, $tags);
			
			if ($outFileName === false) {
				$this->epput('Wrong template: %s ', $this->nameGenerator->lastError);
				$this->moveFile($fullFilename, 
						$this->outFolder.'/'.$this->wrongTemplateFolder.'/'.$partFilename);
				continue;
			}
			
			$outFileName = $this->outFolder.'/'.$outFileName;
			
			if (file_exists($outFileName)) {
				$this->pput('Out file exist: %s', $outFileName);
				if ($this->isFileSame($fullFilename, $outFileName)) {
					$this->epput('Duplicate file: %s ', $outFileName);
					$this->moveFile($fullFilename, 
							$this->outFolder.'/'.$this->duplicateFolder.'/'.$partFilename);
					continue;
				}
				$outFileName = $this->findNotExistIndex($outFileName);
				$this->pput('Making new index: %s', $outFileName);
			}
			$this->pput('Move file from "%s" to "%s"', $fullFilename, $outFileName);
			$res = $this->moveFile($fullFilename, $outFileName);
			if (!$res) {
				$this->epput('Can`t move file from "%s" to "%s" ', $fullFilename, $outFileName);
			}
		}
		// check if cur dir is empty
		if (!(new \FilesystemIterator($curFolder))->valid() && ($aFolder !== '/')) {
			$this->pput('Removing empty folder %s', $curFolder);
			rmdir($curFolder);
		}
	}
	
	public function rename($aInFolder, $aOutFolder, $aTemplate) {
		$this->template = $aTemplate;
		$this->inFolder = $aInFolder;
		$this->outFolder = $aOutFolder;
		$this->pput('Start rename files from "%s" to "%s", template "%s"', 
				$aInFolder, $aOutFolder, $aTemplate);
		return $this->renameFilesInFolder('/');
	}
}
