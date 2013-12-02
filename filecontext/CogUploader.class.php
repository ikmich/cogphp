<?php

class CogUploader
{

	private static $uploadedFiles = array();
	private static $num_uploadedFiles = 0;
	private static $num_expectedFiles = 0;
	private static $destination = "default_uploads"; //default value if user does not specify.
	private static $commonFilename;

	public static function retrieveFiles()
	{
		if (!empty($_FILES))
		{
			if (count($_FILES) > 0)
			{
				self::$num_expectedFiles = count($_FILES);

				foreach ($_FILES as $uploadName => $fileToken)
				{
					/* @var $fileToken array */
					//clean the text...
					$uploadName = htmlspecialchars(addslashes($uploadName));
					if (!CogUploadUtil::validate($uploadName))
					{
						if (CogUploadUtil::e_noFileUploaded($uploadName))
						{
							continue;
						}
						else
						{
							break;
						}
					}
					else
					{
						self::$num_uploadedFiles++;
						array_push(self::$uploadedFiles, $fileToken);
					}
				}
			}
		}
	}

	public static function getUploadedFiles()
	{
		return self::$uploadedFiles;
	}

	public static function getUploadedFileCount()
	{
		return count(self::$uploadedFiles);
	}

	public static function getExpectedFileCount()
	{
		return self::$num_expectedFiles;
	}

	public static function getFile($uploadName)
	{
		if (CogUploadUtil::validate($uploadName))
		{
			return $_FILES[$uploadName];
		}
	}

	public static function setCommonFilename($name)
	{
		if ($name !== null && !empty($name))
		{
			self::$commonFilename = $name;
		}
	}

	public static function saveFiles()
	{
		//create destination folder
		CogDir::create(self::$destination);

		$track = 0;
		$moved = false;

		foreach (self::$uploadedFiles as $uploadedFile)
		{
			$track++;
			$tmpname = $uploadedFile["tmp_name"];
			$tmpFilename = $uploadedFile["name"];
			$ext = CogFile::getExtension($tmpFilename);

			if (@func_num_args() > 0)
			{
				$commonFilename = @func_get_arg(0);
				$to_filename = "{$commonFilename}_{$track}.{$ext}";
			}
			else if (isset(self::$commonFilename))
			{
				$to_filename = self::$commonFilename . "_{$track}.{$ext}";
			}
			else
			{
				$to_filename = "{$tmpFilename}";
			}

			$from_filepath = $tmpname;
			$to_filePath = self::$destination . "/{$to_filename}";

			$moved = @move_uploaded_file($from_filepath, $to_filePath);
			if (!$moved)
				return false;
		}

		return $moved;
	}

	public static function setDestination($folderPath)
	{
		self::$destination = $folderPath;
	}

}

?>
