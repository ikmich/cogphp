<?php

class CogUploadUtil
{

	private static $errorMsg;

	public static function fileUploaded($uploadName)
	{
		if (self::e_noFileUploaded($uploadName))
		{
			return false;
		}
		return true;
	}

	/**
	 * Clean up a string expected to be a file path.
	 * 
	 * @param string $str
	 * @return string
	 */
	public static function sanitize($str)
	{
		if ($str !== null & !empty($str))
		{
			return htmlspecialchars(addslashes($str));
		}
	}

	/**
	 * Performs a validation to check the state of the uploaded file.
	 * 
	 * @param string $uploadName <p>The upload name given to the file.</p>
	 * @return boolean
	 */
	public static function validate($uploadName)
	{
		$uploadName = self::sanitize($uploadName);
		if (!empty($_FILES) && isset($_FILES[$uploadName]))
		{
			$uploadedFile = $_FILES[$uploadName];
		}
		else
		{
			return false;
		}

		if (self::e_noError($uploadName))
		{
			if (is_uploaded_file($uploadedFile["tmp_name"]))
			{
				//integrity validated. do type-mapping here?
				return true;
			}
			else
			{
				//Possible file-upload attack? Send email to admin?...
				//research more on the is_uploaded_file() method
				return false;
			}
		}
		else
		{
			if (self::e_exceedsPhpUploadSize($uploadName))
			{
				self::$errorMsg = "ERROR: {$uploadedFile['name']} exceeds script limit.";
				return false;
			}

			if (self::e_exceedsFormUploadSize($uploadName))
			{
				self::$errorMsg = "ERROR: {$uploadedFile['name']} is too large.";
				return false;
			}

			if (self::e_isPartialUpload($uploadName))
			{
				self::$errorMsg = "ERROR: {$uploadedFile['name']} was partially uploaded.";
				return false;
			}

			if (self::e_noFileUploaded($uploadName))
			{
				self::$errorMsg = "ERROR: No file uploaded.";
				return false;
			}

			if (self::e_noTempFolder($uploadName))
			{
				self::$errorMsg = "ERROR: No temp folder.";
				return false;
			}

			if (self::e_couldNotWriteToDisk($uploadName))
			{
				self::$errorMsg = "ERROR: Failed to write to disk.";
				return false;
			}

			if (self::e_uploadStoppedByExtension($uploadName))
			{
				self::$errorMsg = "ERROR: Upload was stopped by extension.";
				return false;
			}
		}
	}

	public static function getErrorMsg()
	{
		return self::$errorMsg;
	}

	/**
	 * Checks if there is no error during a file upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_noError($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if an "exceeds php upload size" error exists during a file
	 * upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_exceedsPhpUploadSize($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if an "exceeds form upload size" error exists during a file 
	 * upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_exceedsFormUploadSize($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 2)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if an "is partial upload" error exists during a file upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_isPartialUpload($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 3)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if a "no file uploaded" error exists during a file upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_noFileUploaded($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 4)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if a "no temp folder" error exists during a file upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_noTempFolder($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 6)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if a "could not write to disk" error exists during a file upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_couldNotWriteToDisk($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 7)
		{
			return true;
		}
		return false;
	}

	/**
	 * Checks if an "upload stopped by extension" error exists during a file
	 * upload.
	 * 
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function e_uploadStoppedByExtension($uploadName)
	{
		if (isset($_FILES[$uploadName]) && $_FILES[$uploadName]["error"] == 9)
		{
			return true;
		}
		return false;
	}

	/**
	 * Performs a check if the file specified is an uploaded file.
	 * 
	 * @param string $tmpName
	 * @return boolean
	 */
	public static function isUploadedFile($tmpName)
	{
		return is_uploaded_file($tmpName);
	}
}

?>
