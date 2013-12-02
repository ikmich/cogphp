<?php

class CogFile
{

	/**
	 * Creates and opens a file for read and write.
	 * 
	 * @param string $filename The path to the file to open.
	 * @return resource The file pointer resource.
	 */
	public static function create($path)
	{
		$path = CogFS::normalize($path);

		$parentDir = dirname($path);
		CogDir::create($parentDir);

		//if the file does not exist, create it.
		if (!is_file($path))
		{
			return fopen($path, CogFileOpenMode::$BIN_CREATE_READ_WRITE);
		}
	}

	/**
	 * Deletes a file.
	 * 
	 * @param string $filepath
	 * @return boolean TRUE if successful; FALSE otherwise.
	 */
	public static function delete($filepath)
	{
		$filepath = CogFS::normalize($filepath);
		if (is_file($filepath))
		{
			return unlink($filepath);
		}
	}

	/**
	 * Gets the extension of a file from its filepath.
	 * 
	 * @param string $filePath The path to the file whose you want to get.
	 * @return string
	 */
	public static function getExtension($filePath)
	{
		$filePath = CogFS::normalize($filePath);
		$ext = pathinfo($filePath, PATHINFO_EXTENSION);
		return $ext;
	}

	/**
	 * Checks if a file's extension falls within a range of 
	 * comma-separated extensions.
	 * 
	 * @param string $filePath The path to the file.
	 * @param string $permittedExts A comma-separated string of file extensions.
	 * @return boolean
	 */
	public static function checkExtension($filePath, $permittedExts)
	{
		$filePath = CogFS::normalize($filePath);
		$flag = false;

		//replace comma-and-space with just comma...
		$permittedExts = preg_replace("<\,\s*>", ",", $permittedExts);

		//create array from the comma-separated string...
		$permittedExtsArray = explode(",", $permittedExts);

		foreach ($permittedExtsArray as $permittedExt)
		{
			//replace leading dot if user added it to the supplied extension names
			$permittedExt = preg_replace('<^\.>', '', $permittedExt);
			if (self::getExtension($filePath) === $permittedExt)
			{
				$flag = true;
				break;
			}
		}
		return $flag;
	}

	/**
	 * Gets the contents of a file as a string.
	 * 
	 * @param string $filePath
	 * @return string
	 */
	public static function getContents($filePath)
	{
		$filePath = CogFS::normalize($filePath);
		if (is_file($filePath))
		{
			$fileContents = file_get_contents($filePath);
			if ($fileContents === false)
			{
				return null;
			}
			return $fileContents;
		}
	}

	public static function erase($filepath)
	{
		self::write($filepath, '');
	}

	/**
	 * Writes a string to a file.
	 * 
	 * @param string $filepath The path to the file.
	 * @param string $data The string to write to the file.
	 * @return boolean TRUE if successful; FALSE otherwise.
	 */
	public static function write($filepath, $data)
	{
		$filepath = CogFS::normalize($filepath);

		//create the file if it does not exist.
		if (!file_exists($filepath))
		{
			self::create($filepath);
		}

		//do the write.
		if (file_put_contents($filepath, $data))
		{
			return true;
		}

		return false;
	}

	public static function writeLn($filepath, $data)
	{
		$filepath = CogFS::normalize($filepath);
		if (filesize($filepath) > 0)
		{
			self::write($filepath, "\r\n" . $data);
		}
		else
		{
			self::write($filepath, $data);
		}
	}

	/**
	 * Appends a string to a file.
	 * 
	 * @param string $filepath The path to the file.
	 * @param string $data The string to append to the file.
	 * @return boolean TRUE if successful; FALSE otherwise.
	 * <p>For more extensive options for file writing, use the CogFileWriter class.</p>
	 */
	public static function append($filepath, $data)
	{
		$filepath = CogFS::normalize($filepath);

		if (!(CogFile::Exists($filepath)))
		{
			CogFile::create($filepath);
		}

		if (file_put_contents($filepath, $data, FILE_APPEND))
		{
			return true;
		}

		return false;
	}

	public static function appendLn($filepath, $data)
	{
		$filepath = CogFS::normalize($filepath);
		if (!file_exists($filepath))
		{
			CogFile::create($filepath);
		}

		if (filesize($filepath) > 0)
		{
			return self::append($filepath, "\r\n" . $data);
		}
		else
		{
			return self::append($filepath, $data);
		}
	}

	/**
	 * Gets the size in bytes of a file.
	 * 
	 * @param string $filepath The path to the file.
	 * @return mixed The size of the file if successful; FALSE otherwise.
	 */
	public static function getSize($filepath)
	{
		$filepath = CogFS::normalize($filepath);
		if (is_file($filepath))
		{
			return filesize($filepath);
		}
		else
		{
			Cog::errorNotice("File does not exist.");
		}
	}

	/**
	 * Checks if a file has a file extension.
	 * 
	 * @param string $filePath The path to the file.
	 * @return boolean
	 */
	public static function hasExtension($filePath)
	{
		$extPattern = "<\.[a-zA-Z0-9]+$>";
		$b = preg_match($extPattern, $filePath);
		return $b;
	}

	/**
	 * Removes the file extension part from the filepath/filename.
	 * 
	 * @param string $filepath The path to the file.
	 * @return string
	 */
	public static function removeExtension($filepath)
	{
		$extPattern = "<\.[a-zA-Z0-9]+$>";
		return preg_replace($extPattern, '', $filepath);
	}

	/**
	 * Gets the name of a file (label plus extension) from the file path.
	 * 
	 * @param string $filepath
	 * @return string The name of the file.
	 */
	public static function getName($filepath)
	{
		return basename(CogFS::normalize($filepath));
	}

	/**
	 * Gets a file's name without the extension.
	 * 
	 * @param string $filepath The path to the file.
	 * @return string The file's label (file name without the extension).
	 */
	public static function getLabel($filepath)
	{
		$filename = self::getName($filepath);
		$extPattern = "<\.[a-zA-Z0-9]+$>";

		if (self::hasExtension($filename))
		{
			return preg_replace($extPattern, '', $filename);
		}
		return $filename;
	}

	/**
	 * Gets the path to the folder that contains a file.
	 * 
	 * @param string $filepath The filepath to the file in question.
	 * @return string The path of the folder that contains the file.
	 */
	public static function getFolderPath($filepath)
	{
		return dirname(CogFS::normalize($filepath));
	}

	/**
	 * Gets the name of the folder that contains a file.
	 * 
	 * @param string $filepath The path to the file.
	 * @return string The name of the folder that contains the file.
	 */
	public static function getFolderName($filepath)
	{
		return CogDir::getName(self::getFolderPath($filepath));
	}

	/**
	 * Checks if a file exists.
	 * 
	 * @param string $filepath The path to the file.
	 * @return boolean
	 */
	public static function exists($filepath)
	{
		$filepath = CogFS::normalize($filepath);
		return file_exists($filepath);
	}

	/**
	 * Checks if a file is a valid file.
	 * 
	 * @param string $filepath The path to the file.
	 * @return boolean
	 */
	public static function isValid($filepath)
	{
		$filepath = CogFS::normalize($filepath);
		return file_exists($filepath) && is_file($filepath);
	}

	/**
	 * Checks if a file is empty.
	 * 
	 * @param string $filepath The path to the file.
	 * @return boolean
	 */
	public static function isEmpty($filepath)
	{
		if (filesize(CogFS::normalize($filepath)) === 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Copies a file to a new file path.
	 * 
	 * @param string $filePath The path to the file.
	 * @param string $newFilePath The new file path.
	 * @return boolean
	 */
	public static function copy($filePath, $newFilePath)
	{
		$filePath = CogFS::normalize($filePath);
		$newFilePath = CogFS::normalize($newFilePath);

		if (is_file($filePath))
		{
			$flag = copy($filePath, $newFilePath);
			return $flag;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Moves a file from one location to another.
	 * 
	 * @param string $oldPath The current path to the file.
	 * @param string $newPath The path representing the new location.
	 * @return boolean
	 */
	public static function move($oldPath, $newPath)
	{
		return rename($oldPath, $newPath);
	}

	/**
	 * Closes a file.
	 * 
	 * @param resource $fileHandle The file handle returned by PHP fopen() or fsockopen().
	 * @return boolean
	 */
	public static function close($fileHandle)
	{
		//wrapper for fclose()
		if ($fileHandle)
		{
			$b = fclose($fileHandle);
			return $b;
		}
		return false;
	}

	/**
	 * Tests for the end of file. A wrapper function around PHP feof().
	 * 
	 * @param resource $fileHandle A valid file handle returned by PHP's file-open functions.
	 * @return boolean
	 */
	public static function endOfFile($fileHandle)
	{
		//wrapper for feof()
		if ($fileHandle)
		{
			$b = feof($fileHandle);
			return $b;
		}
		return false;
	}

	/**
	 * 'Saves' the contents of the output buffer to a file.
	 * It's a wrapper function for PHP's fflush().
	 * 
	 * @param mixed $arg A valid file handle returned by PHP's file-open functions; or the path to the file.
	 * @return boolean TRUE on success; FALSE otherwise.
	 */
	public static function saveOutputBufferToFile($arg)
	{
		if (gettype($arg) == "resource")
		{
			$b = fflush($arg);
		}
		else if (gettype($arg) == "string")
		{
			$op = new CogFileOpener($arg);
			$op->setMode(CogFileOpenMode::$BIN_WRITE_APPEND);
			$handle = $op->openFile();
			self::saveOutputBufferToFile($handle);
		}
		return $b;
	}

	/**
	 * Outputs/sends the response to the output response stream.
	 * 
	 * @param string $filepath
	 */
	public static function sendResponse($filepath)
	{
		if (is_file($filepath))
		{
			$fp = fopen($filepath, CogFileOpenMode::$BIN_READ_WRITE_APPEND);
			readfile($filepath);
			fclose($fp);
		}
	}

	/**
	 * Gets a character from a file until the end of file (EOF) is reached.
	 * 
	 * @param resource $fileHandle A valid file handle returned by PHP's file-open functions.
	 * @return mixed The character or FALSE on EOF.
	 */
	public static function getChar($fileHandle)
	{
		//wrapper for PHP's fgetc()
		$val = @fgetc($fileHandle);
		if ($val !== false)
		{
			return $val;
		}
		return false;
	}

	/**
	 * Gets the time a file was last accessed.
	 * 
	 * @param string $filePath The path to the file.
	 * @return mixed The time of last access; or FALSE on failure.
	 */
	public static function getLastAccessTime($filePath)
	{
		if (CogFile::isValid($filePath))
		{
			return fileatime($filePath);
		}
	}

	/**
	 * Gets the time a file was last modified.
	 * 
	 * @param string $filepath The path to the file.
	 * @return mixed The last modified time; or FALSE on failure.
	 */
	public static function getModifiedTime($filepath)
	{
		if (CogFile::isValid($filepath))
		{
			return filemtime($filepath);
		}
	}

	/**
	 * Gets the owner id of the file.
	 * 
	 * @param string $filepath The path to the file.
	 * @return mixed The user id of the file or FALSE on failure.
	 */
	public static function getOwnerId($filepath)
	{
		if (CogFile::isValid($filepath))
		{
			return fileowner($filepath);
		}
	}

	/**
	 * Gets the permissions on a file.
	 * 
	 * @param string $filepath The path to the file.
	 * @return mixed The permissions (int); or FALSE on failure.
	 */
	public static function getPermissions($filepath)
	{
		if (CogFile::isValid($filepath))
		{
			return fileperms($$filepath);
		}
	}

	/**
	 * Returns the type of file.
	 * Possible values are: 'fifo', 'dir', 'file', 'char', 'block', 'link', 'socket', 'unknown'
	 * 
	 * @param string $filepath The path to the file.
	 * @return mixed The type of the file; or FALSE on failure.
	 */
	public static function getFiletype($filepath)
	{
		if (CogFile::isValid($filepath))
		{
			return filetype($filepath);
		}
	}
	/**
	 * Reads a file and writes the contents to the output buffer.
	 * 
	 * @param resource $filehandle
	 * @return mixed The number of characters passed; or FALSE on failure.
	 */
//	public static function toOutputStream($arg) {
//		if (gettype($arg) == 'resource') {
//			return fpassthru($arg);
//		}
//		else if (gettype($arg) == 'string') {
//			$op = new CogFileOpener($arg);
//			$op->setMode(CogFileOpenMode::$BIN_READ);
//			$fH = $op->openFile();
//			self::toOutputStream($fH);
//		}
//	}
}

//end class CogFile
?>