<?php

class CogCsvReader
{

	//The file handle of the csv file.
	private $filehandle;
	//The length of the file to read.
	private $length = 0; //defaults to 0
	//The delimiter character. defaults to comma
	private $delimiter = ',';
	//The field enclosure character. defaults to double quote
	private $enclosure = '"';
	private $firstLine; //array
	private $fileHasBeenRead = false;

	/**
	 * The escape character.
	 * @version 5.3.0
	 * @var string
	 * Would be ommitted here to avoid version incompatibility.
	 */
	private $escapeChar = '\\';

	/**
	 * $fileData
	 * @var array
	 * 2-dim array of the csv file contents
	 */
	private $fileData = array();

	/**
	 * Constructor.
	 * @param resource $filehandle optional
	 */
	public function __construct()
	{
		if (@func_num_args() > 0) {
			$arg = @func_get_arg(0);
			if (is_resource($arg)) {
				//file handle
				$this->filehandle = $arg;
			}
			else if (is_string($arg)) {
				//file path. open file and create file handle
				$opener = new CogFileOpener($arg);
				$opener->setMode(CogFileOpenMode::$READ);
				$fh = $opener->open();
				//set the file handle
				$this->filehandle = $fh;
				//destroy vars
				unset($opener);
				unset($fh);
			}
		}
	}

	/**
	 * Sets the file handle, if not specified in constructor.
	 * @param resource $filehandle
	 */
	public function setFilehandle($filehandle)
	{
		//if fileHandle not set in constructor, it can be set here. or if this object is to be used again.
		if ($filehandle !== null) {
			$this->filehandle = $filehandle;
		}
	}

	/**
	 * Sets the length of the file to read.
	 * @param integer $len
	 */
	public function setLength($len)
	{
		if (!is_null($len) && is_numeric($len)) {
			$this->length = $len;
		}
	}

	/**
	 * Sets the delimiter character for each field. Defaults to comma (,)
	 * @param string $delim
	 */
	public function setDelimiter($delim)
	{
		if (!is_null($delim) && is_string($delim)) {
			$this->delimiter = $delim;
		}
	}

	/**
	 * Sets the enclosure character for a field.
	 * @param string $enclosure
	 */
	public function setFieldEnclosure($enclosure)
	{
		if (!is_null($enclosure) && is_string($enclosure)) {
			$this->enclosure = $enclosure;
		}
	}

	/**
	 * Sets the escape character.
	 * 
	 * @param string $escapeChar
	 * @version 5.3.0
	 */
	public function setEscapeCharacter($escapeChar)
	{
		if ($escapeChar !== null) {
			$this->escapeChar = $escapeChar;
		}
	}

	/**
	 * Returns the first line of the csv file. Useful where the first line contains the field names.
	 * @return array
	 */
	public function getFirstLine()
	{
		//read the first line
		if ($this->fileHasBeenRead) {
			return $this->fileData[0];
		}
		//else...read the file and return the first line...
		while (($firstLine = fgetcsv($this->filehandle)) !== FALSE) {
			$this->firstLine = $firstLine;
			return $firstLine;
		}
	}

	/**
	 * Get the field names, assumed to be the first line of the csv file.
	 * @return array
	 */
	public function getFieldNames()
	{
		return $this->getFirstLine();
	}

	/**
	 * Get the number of fields or columns in the csv file.
	 * @return integer
	 */
	public function getNumFields()
	{
		if (is_null($this->firstLine)) {
			return count($this->getFirstLine());
		}
		return count($this->firstLine);
	}

	/**
	 * Read a line and move pointer to the next line.
	 * @return array An array containing the contents of one line in the csv file.
	 */
	public function readLine()
	{
		//fgetcsv($handle, $length, $delimiter, $enclosure, $escape)
		$args = array();
		$line;
		if (!is_null($this->filehandle)) {
			array_push($args, $this->filehandle);
			if (!is_null($this->length) || $this->length != 0) {
				array_push($args, $this->length);
			}
			else {
				array_push($args, null);
			}
			array_push($args, $this->delimiter, $this->enclosure);
		}
		$line = call_user_func_array('fgetcsv', $args);
		return $line;
	}

	/**
	 * Read the file and return the contents.
	 * @return array A 2-dimensional array containing the file contents
	 */
	public function readFile()
	{
		while (($line = $this->readLine()) !== false) {
			$this->fileData[] = $line;
		}
		$this->fileHasBeenRead = true;
		return $this->fileData;
	}

	/**
	 * Alias for readFile()
	 */
	public function read()
	{
		return $this->readFile();
	}

	/**
	 * Close the file
	 * @return type boolean
	 */
	public function close()
	{
		return fclose($this->filehandle);
	}
}

?>
