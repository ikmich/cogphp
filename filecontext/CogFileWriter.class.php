<?php

class CogFileWriter
{

	/**
	 * The number of bytes to be written.
	 * 
	 * @var int 
	 */
	private $length;

	/**
	 * The file resource handle to write to.
	 * 
	 * @var resource 
	 */
	private $filehandle = null;

	/**
	 * The string to write to the file.
	 * 
	 * @var string 
	 */
	private $stringToWrite;

	/**
	 * The file path for the file to write to.
	 * 
	 * @var string 
	 */
	private $filepath;

	/**
	 * An array of data to write to the file.
	 * 
	 * @var array 
	 */
	private $dataToWrite = array();

	/**
	 * Boolean desire to reference the include_path of php's config.
	 * Used when implementing the file_put_contents() php function.
	 * 
	 * @var boolean 
	 */
	private $flag_use_include_path;

	/**
	 * Indicates desire to get an exclusive lock on the file while writing.
	 * Used when implementing the file_put_contents() php function.
	 * 
	 * @var boolean 
	 */
	private $flag_lock_ex;

	/**
	 * Indicates the desire to append the new data to the file.
	 * Used when implementing the file_put_contents() php function.
	 * 
	 * @var boolean 
	 */
	private $flag_file_append;

	/**
	 * The stream context resource to use with the file_put_contents() php
	 * function.
	 * 
	 * @var resource 
	 */
	private $stream_context;

	/*
	 * behaviours...
	 */
	private $using_string;
	private $using_data;
	private $using_flags;
	private $using_context;

	/**
	 * Boolean indicating that data should be written to a new line in the file.
	 * 
	 * @var boolean 
	 */
	private $using_newline = false;
//	private $using_function_fwrite = true;
//	private $using_function_file_put_contents;
	private $isSuccessful = false;
	private $newline = "\r\n";

	/**
	 * The constructor
	 * @param mixed Optional argument representing a file resource handle or
	 * a file path.
	 */
	public function __construct()
	{
		if (@func_num_args() > 0)
		{
			$arg = @func_get_arg(0);
			if (gettype($arg) == "resource")
			{
				$this->setFilehandle($arg);
			}
			else if (gettype($arg) == "string")
			{
				$this->setFilepath($arg);
			}
		}
	}

	private function setUsingNewLine()
	{
		$this->using_newline = true;
	}

	private function isUsingNewLine()
	{
		return $this->using_newline;
	}

	public function setLength($len)
	{
		$this->length = $len;
	}

	public function setFilehandle($filehandle)
	{
		$this->filehandle = $filehandle;
	}

	public function getFilehandle()
	{
		return $this->filehandle;
	}

	public function setFilepath($filepath)
	{
		$this->filepath = $filepath;
	}

	public function getFilepath()
	{
		return $this->filepath;
	}

	/**
	 * Sets the data to write to the file. Can be a string, array, or a 
	 * stream resource.
	 * 
	 * @param mixed $data string, array or stream resource.
	 */
	public function setData($data)
	{
		$this->dataToWrite = $data;
		//$this->using_data = true;
	}

	private function isUsingFlags()
	{
		return $this->flag_file_append
			|| $this->flag_use_include_path
			|| $this->flag_file_append;
	}

	/**
	 * Indicates that php's configured include path should be used.
	 */
	public function useIncludePath()
	{
		$this->flag_use_include_path = true;
	}

	/**
	 * Indicates desire to lock the file while writing to it.
	 * If set, the LOCK_EX flag will be used with the file_put_contents()
	 * php function.
	 */
	public function lockFile()
	{
		$this->flag_lock_ex = true;
	}

	/**
	 * Set the stream context, which if set, would be used as an argument of 
	 * the php file_put_contents() function. 
	 * 
	 * @param resource $stream_context 
	 */
	public function setStreamContext($stream_context)
	{
		$this->stream_context = $stream_context;
	}

	public function getStreamContext()
	{
		return $this->stream_context;
	}

	/**
	 * Indicates that data written to the file should not overwrite the file
	 * contents, but append it to the end of the file. 
	 */
	public function useAppendMode()
	{
		$this->flag_file_append = true;
	}

	/**
	 * Checks if the write was successful.
	 * 
	 * @return boolean 
	 */
	public function isSuccessful()
	{
		return $this->isSuccessful;
	}

	private function setSuccessful($bool)
	{
		$this->isSuccessful = $bool;
	}

	/**
	 * Writes data to the file starting on a new line unless the file
	 * is empty. 
	 * 
	 * @param string $data The data to write. It's optional, as the data to write
	 * might have been previously specified with the setData() method of this class.
	 */
	public function writeLn()
	{
		$this->setUsingNewLine();

		if (@func_num_args() > 0)
		{
			$data = @func_get_arg(0);
			$this->setData($data);
		}

		$this->write();
	}

	/**
	 * Appends data to the file. Does not ovewrite the previous contents.
	 * 
	 * @param string $data The data to append. Optional, as the data might
	 * have been previously specified with the setData() method of this class.
	 */
	public function append()
	{
		$this->useAppendMode();

		if (@func_num_args() > 0)
		{
			$data = @func_get_arg(0);
			$this->setData($data);
		}

		$this->write();
	}

	public function appendLn()
	{
		$this->useAppendMode();
		$this->setUsingNewLine();

		if (@func_num_args() > 0)
		{
			$data = @func_get_arg(0);
			$this->setData($data);
		}

		$this->write();
	}

	/**
	 * Writes data to the file. 
	 * 
	 * @param string $data The data to write to the file. It is optional, as the
	 * data could have previously been specified using the setData() method of this class.
	 * @return boolean 
	 */
	public function write($data = null)
	{
		if (isset($data))
		{
			$this->setData($data);
		}

		//if no data to write, dont write anything.
		if (!isset($this->dataToWrite))
		{
			return false;
		}

		$fn_args_fwrite = array();
		$fn_args_file_put_contents = array();

		if (!isset($this->filehandle))
		{
			//try and get the file handle from the file path if set
			if (isset($this->filepath))
			{
				if ($this->flag_file_append)
				{
					//open file with append mode...
					$this->filehandle = fopen($this->filepath, CogFileOpenMode::$BIN_WRITE_APPEND);
				}
				else
				{
					//open file with overwrite mode..
					$this->filehandle = fopen($this->filepath, CogFileOpenMode::$BIN_WRITE);
				}
			}
			else
			{
				//no filehandle, no filepath..
				return false;
			}
		}

		/*
		 * at this point the filehandle should be available..
		 * also, if the filepath is not available at this point, 
		 * it should return false..
		 */
		if (!isset($this->filepath))
		{
			return false;
		}

		/*
		 * at this point, both the filehandle and the filepath are available..
		 */

		if ($this->isUsingNewLine())
		{
//			Cog::printLnOrange('using new line');
			if (filesize($this->filepath) > 0)
			{
				if (gettype($this->dataToWrite) == "string")
				{
					$this->dataToWrite = $this->newline . $this->dataToWrite;
				}
				else if (gettype($this->dataToWrite == "array"))
				{
					$newdata = array($this->newline);
					$this->dataToWrite = array_merge($newdata, $this->dataToWrite);
				}
				else if (gettype($this->dataToWrite) == "resource")
				{
					//hmmmm....
				}
			}
		}
		else
		{
//			Cog::printLnOrange('not using new line');
		}

		array_push($fn_args_fwrite, $this->filehandle);
		array_push($fn_args_file_put_contents, $this->filepath);

		if ($this->isUsingFlags() || isset($this->stream_context))
		{
			/*
			 * use file_put_contents..
			 */
			if (isset($this->dataToWrite))
			{
				array_push($fn_args_file_put_contents, $this->dataToWrite);

				if ($this->isUsingFlags())
				{
					$flags = null;
					if ($this->flag_file_append === true)
					{
						$flags = FILE_APPEND;
					}
					if ($this->flag_lock_ex === true)
					{
						$flags = $flags | LOCK_EX;
					}
					if ($this->flag_use_include_path === true)
					{
						$flags = $flags | FILE_USE_INCLUDE_PATH;
					}
					array_push($fn_args_file_put_contents, $flags);
				}
				else
				{
					array_push($fn_args_file_put_contents, null);
				}

				if (isset($this->stream_context))
				{
					array_push($fn_args_file_put_contents, $this->stream_context);
				}

				//call the file_put_contents(...) function..
				$bytes = call_user_func_array('file_put_contents', $fn_args_file_put_contents);

				if ($bytes === false)
				{
					$this->setSuccessful(false);
				}
				else
				{
					$this->setSuccessful(true);
				}

				return $bytes;
			}
			else
			{
				//no data to write.. 
				return false;
			}
		}
		else
		{
//			Cog::printlnblue('using fwrite');
			/*
			 * use fwrite(...)
			 */
			if (isset($this->dataToWrite))
			{
				if (gettype($this->dataToWrite) == "array")
				{
					foreach ($this->dataToWrite as $item)
					{
						$this->stringToWrite .= $item;
					}
					$this->dataToWrite = $this->stringToWrite;
				}
				array_push($fn_args_fwrite, $this->dataToWrite);

				if (isset($this->length))
				{
					array_push($fn_args_fwrite, $this->length);
				}

				//call the fwrite(...) php function..
				$bytes = call_user_func_array('fwrite', $fn_args_fwrite);

				if ($bytes === false)
				{
					$this->setSuccessful(false);
				}
				else
				{
					$this->setSuccessful(true);
				}

				return $bytes;
			}
		}



//		if (isset($this->filehandle)) {
//			return $this->fn_fwrite();
//		}
//		else if ($this->filepath !== null && $this->filehandle === null) {
//			return $this->fn_put_contents();
//		}
//		else if ($this->filehandle !== null && $this->filepath !== null) {
//			if ($this->using_function_file_put_contents) {
//				return $this->fn_put_contents();
//			}
//			else if ($this->using_function_fwrite) {
//				return $this->fn_fwrite();
//			}
//		}
//		else {
//			//neither filehandle nor filepath provided.
//			return false;
//		}
	}

//	private function fn_fwrite() {
//		$bytes = null;
//		$fn_args = array();
//
//		if (!is_null($this->filehandle)) {
//			array_push($fn_args, $this->filehandle);
//			if (isset($this->stringToWrite)) {
//				if ($this->using_newline === true) {
//					$this->stringToWrite = $this->stringToWrite . "\r\n";
//				}
//				array_push($fn_args, $this->stringToWrite);
//			}
//			else {
//				if (!is_null($this->dataToWrite)) {
//					if (gettype($this->dataToWrite) == "string") {
//						$this->stringToWrite = $this->dataToWrite;
//					}
//					else if (gettype($this->dataToWrite) == "array") {
//						//$this->stringToWrite = implode("", $this->data);
//						foreach ($this->dataToWrite as $line) {
//							$this->stringToWrite .= $line . "\r\n";
//						}
//						//remove trailing newline chars..
//						$this->stringToWrite = preg_replace('/(\r\n)+$/', "", $this->stringToWrite);
//					}
//					else if (gettype($this->dataToWrite) == "stream") {
//						//condition uncertain...
//					}
//					if ($this->using_newline === true) {
//						$this->stringToWrite = $this->stringToWrite . "\r\n";
//					}
//					array_push($fn_args, $this->stringToWrite);
//				}
//			}
//
//			if ($this->length !== null) {
//				array_push($fn_args, $this->length);
//			}
//			$bytes = call_user_func_array('fwrite', $fn_args);
//			if ($bytes !== null) {
//				$this->isSuccessful = true;
//			}
//			return $bytes;
//		}
//	}
//	private function fn_put_contents() {
//		$bytes;
//		$args = array();
//		$flagsArg;
//
//		if ($this->filepath !== null /* && $this->data !== null */) {
//			$this->doFileCheck();
//			array_push($args, $this->filepath);
//			if ($this->dataToWrite === null) {
//				if ($this->stringToWrite !== null) {
//					$this->dataToWrite = $this->stringToWrite;
//				}
//				else {
//					$this->dataToWrite = ""; //empty string
//				}
//			}
//			if ($this->using_newline === true) {
//				if (!CogFile::isEmpty($this->filepath)) {
//					$this->dataToWrite = "\r\n" . $this->dataToWrite;
//				}
//			}
//			array_push($args, $this->dataToWrite);
//
//			if ($this->using_flags === true) {
//				if ($this->flag_file_append && $this->flag_lock_ex && $this->flag_use_include_path) {
//					$flagsArg = FILE_APPEND | LOCK_EX | FILE_USE_INCLUDE_PATH;
//				}
//				else if ($this->flag_file_append && $this->flag_lock_ex) {
//					$flagsArg = FILE_APPEND | LOCK_EX;
//				}
//				else if ($this->flag_file_append && $this->flag_use_include_path) {
//					$flagsArg = FILE_APPEND | FILE_USE_INCLUDE_PATH;
//				}
//				else if ($this->flag_lock_ex && $this->flag_use_include_path) {
//					$flagsArg = LOCK_EX | FILE_USE_INCLUDE_PATH;
//				}
//				else if ($this->flag_file_append) {
//					$flagsArg = FILE_APPEND;
//				}
//				else if ($this->flag_lock_ex) {
//					$flagsArg = LOCK_EX;
//				}
//				else if ($this->flag_use_include_path) {
//					$flagsArg = FILE_USE_INCLUDE_PATH;
//				}
//				array_push($args, $flagsArg);
//			}
//			if ($this->stream_context !== null) {
//				array_push($args, $this->stream_context);
//			}
//
//			$bytes = call_user_func_array('file_put_contents', $args);
//			if ($bytes !== null) {
//				$this->isSuccessful = true;
//			}
//			return $bytes;
//		}
//	}

	public function close()
	{
		if ($this->filehandle)
		{
			fclose($this->filehandle);
		}
	}
}

?>
