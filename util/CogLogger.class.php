<?php

class CogLogger
{

	private static $instance;
	private static $filepath;
	private static $NL = "\r\n";

	/**
	 * The text to log.
	 * 
	 * @var string
	 */
	private $logText = "";

	private function __construct()
	{
		
	}

	/**
	 * Gets the CogLogger (singleton) instance.
	 * 
	 * @param string $filename The file path of the log file.
	 * @return CogLogger
	 */
	public static function getLogger($filepath)
	{
		//Create/open othe file.
		$fp = CogFile::create($filepath);
		if ($fp)
		{
			fclose($fp);
		}

		//Set the file path for the CogLogger instance.
		self::$filepath = CogFS::normalize($filepath);

		//Return the singleton instance.
		if (!isset(self::$instance))
		{
			self::$instance = new CogLogger();
		}

		return self::$instance;
	}

	public function setFilepath($filepath)
	{
		self::$filepath = $filepath;
		return $this;
	}

	public function log($item)
	{
		$this->logText .= "<log>" . self::$NL;

		if (get_class($item) == 'CogError')
		{
			/* @var $item CogError */
			$this->fnAddLogLine($item->getDate());
			$this->fnAddLogLine("File: " . $item->getFile() . " [{$item->getLineNumber()}]");
			$this->fnAddLogLine('"' . $item->getMessage() . '"');
			$this->fnAddLogLine($item->getType() . "[{$item->getTypeCode()}]");
		}
		else if (is_string($item))
		{
			/* @var $item stdClass */
			date_default_timezone_set('Africa/Lagos');
			$date = date('Y-m-d H:i:s (T)');
			$this->fnAddLogLine($date);
			$this->fnAddLogLine($item);
		}

		$this->logText .= "</log>" . self::$NL;
		$this->fnAddLogLine(self::$NL);

		$fp = fopen(self::$filepath, CogFileOpenMode::$BIN_WRITE_APPEND);
		fwrite($fp, $this->logText);
		fclose($fp);
	}

	private function fnAddLogLine($msg)
	{
		$this->logText .= $msg . self::$NL;
	}
}

?>
