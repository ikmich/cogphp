<?php

/**
 * Wrapper class for the error object returned by the error_get_last() 
 * function in PHP, and the values it returns.
 */
class CogError
{

	private $type;
	private $msg;
	private $file;
	private $line;
	private $date;

	public function __construct()
	{
		$err = error_get_last();

		$this->type = $err['type'];
		$this->msg = $err['message'];
		$this->file = $err['file'];
		$this->line = $err['line'];

		//don't depend on the server timezone... set yours...
		date_default_timezone_set('Africa/Lagos');
		$this->date = date('Y-m-d H:i:s (T)');
	}

	/**
	 * Returns the error-type code.
	 * 
	 * @return integer
	 */
	public function getTypeCode()
	{
		return $this->type;
	}

	/**
	 * Returns the error message.
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		return $this->msg;
	}

	/**
	 * Returns the file path of the file with the error.
	 * 
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Returns the line number at which the error was logged.
	 * 
	 * @return integer
	 */
	public function getLineNumber()
	{
		return $this->line;
	}

	/**
	 * Returns string describing the type of error.
	 * 
	 * @return string
	 */
	public function getType()
	{
		$type = $this->getTypeCode();
		switch ($type)
		{
			case 1:
				return 'E_ERROR (integer)';
			case 2:
				return 'E_WARNING (integer)';
			case 4:
				return 'E_PARSE (integer)';
			case 8:
				return 'E_NOTICE (integer)';
			case 16:
				return 'E_CORE_ERROR (integer)';
			case 32:
				return 'E_CORE_WARNING (integer)';
			case 64:
				return 'E_COMPILE_ERROR (integer)';
			case 128:
				return 'E_COMPILE_WARNING (integer)';
			case 256:
				return 'E_USER_ERROR (integer)';
			case 512:
				return 'E_USER_WARNING (integer)';
			case 1024:
				return 'E_USER_NOTICE (integer)';
			case 2048:
				return 'E_STRICT (integer)';
			case 4096:
				return 'E_RECOVERABLE_ERROR (integer)';
			case 8191:
				return 'E_ALL (integer)';
			default:
				return 'UNKNOWN TYPE';
		}
	}

	/**
	 * Gets the recorded date of this CogError instance as a string.
	 * 
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}

	public static function getLast()
	{
		$lastError = error_get_last();
		return $lastError['message'];
	}
}

?>