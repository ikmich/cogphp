<?php

class CogUploadItem
{

	private $uploadName;
	private $destinationDir;
	private $defaultDestination = "-default-uploads"; //Relative to the document root.
	private $destinationFilename;
	private $destinationFilepath;
	private $isSuccessful;

	//constructor
	public function __construct($uploadName)
	{
		$uploadName = CogUploadUtil::sanitize($uploadName);
		if (CogUploadUtil::validate($uploadName))
		{
			//...no errors uploading
			$this->uploadName = $uploadName;
			$this->destinationDir = $this->defaultDestination;
		}
		else
		{
			$this->setSuccess(false);
		}
	}

	private function setSuccess($bool)
	{
		$this->isSuccessful = $bool;
	}

	public function setDestination($folderpath)
	{
		$folderpath = CogFS::normalize($folderpath);
		$this->destinationDir = $folderpath;
		return $this;
	}

//	public function saveTo($folderPath)
//	{
//		return $this->setDestination($folderPath);
//	}

	public function getDestination()
	{
		return $this->destinationDir;
	}

	public function setDestinationFilename($filename)
	{
		if ($filename !== NULL && !empty($filename))
		{
			if (CogFile::hasExtension($filename))
			{
				$this->destinationFilename = $filename;
			}
			else
			{
				$ext = CogFile::getExtension($this->getName());
				$this->destinationFilename = $filename . ".{$ext}";
			}
			return $this;
		}
	}

//	public function saveAs($filename)
//	{
//		return $this->setDestinationFilename($filename);
//	}

	public function getDestinationFilename()
	{
		return $this->destinationFilename;
	}

	private function setDestinationFilepath($filepath)
	{
		$this->destinationFilepath = $filepath;
	}

	public function getDestinationFilepath()
	{
		return $this->destinationFilepath;
	}

	public function getUploadName()
	{
		return $this->uploadName;
	}

	/**
	 * Gets the name of the file as specified in the file upload control.
	 * 
	 * @return string <p>The name of the file</p>
	 */
	public function getName()
	{
		if (!empty($_FILES) && isset($_FILES[$this->uploadName]))
		{
			$name = $_FILES[$this->uploadName]["name"];
			//remove possible escape slashes from magic_quotes...
			return stripslashes($name);
		}
		return null;
	}

	/**
	 * Gets the file "label" (the name of the file, without the extension).
	 * 
	 * @return string <p>The file label.</p>
	 */
	public function getLabel()
	{
		return CogFile::getLabel($this->getName());
	}

	public function getExtension()
	{
		//The extension can be gotten from the tmp_name property
		return CogFile::getExtension($this->getName());
	}

	public function getType()
	{
		return $_FILES[$this->uploadName]["type"];
	}

	public function getTempPath()
	{
		$tempPath = $_FILES[$this->uploadName]["tmp_name"];
		return $tempPath;
	}

	public function getError()
	{
		return $_FILES[$this->uploadName]["error"];
	}

	public function getSize()
	{
		return $_FILES[$this->uploadName]["size"];
	}

	public function save()
	{
		if ($this->isSuccessful === false)
		{
			return false;
		}

		//create destination folder...
		if (!CogDir::create($this->destinationDir))
		{
			$this->setSuccess(false);
			return false;
		}

		if (!isset($this->destinationFilename) || empty($this->destinationFilename))
		{
			//no destination filename is set. try and obtain it from the available data..
			$this->destinationFilename = $this->getLabel() . ".{$this->getExtension()}";
		}

		$from_filepath = $this->getTempPath();
		$to_filepath = CogFS::normalize("{$this->destinationDir}/{$this->destinationFilename}");
		$this->setDestinationFilepath($to_filepath);

		$flag = move_uploaded_file($from_filepath, $to_filepath);
		if ($flag)
		{
//			Cog::printlnblue('success');
			$this->setSuccess(true);
		}
		else
		{
//			Cog::printlnred("Could not complete file upload!");
			$this->setSuccess(false);
		}
	}

	public function getSuccess()
	{
		return $this->isSuccessful;
	}
}

?>
