<?php

/**
 * Provides utility interface to push a file to the client response for download.
 */
class CogFileDownload
{

	private $filepath;
	private $filename;
	private $mimetype;

	public function __construct()
	{
		
	}

	/**
	 * Sets the filepath of the file.
	 * 
	 * @param string $filepath
	 */
	public function setFilepath($filepath)
	{
		$this->filepath = $filepath;
		return $this;
	}

	/**
	 * Sets the file name to be presented to the client.
	 * 
	 * @param string $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
		return $this;
	}

	/**
	 * Sets the mime type of the file.
	 * 
	 * @param string $mimeType
	 */
	public function setContentType($mimeType)
	{
		$this->mimetype = $mimeType;
		return $this;
	}

	/**
	 * Pushes the file to the client for download.
	 */
	public function push()
	{
		if (!is_null($this->mimetype) &&
			!is_null($this->filename) &&
			!is_null($this->filepath))
		{
			header("Content-Type: {$this->mimetype}");
			header("Content-Disposition: attachment; filename = {$this->filename}");
			header("Content-Transfer-Encoding: binary");
			@readfile($this->filepath);
		}
	}
}

?>
