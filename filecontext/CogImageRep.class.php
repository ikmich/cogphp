<?php

/**
 * A utility class to work with and on image resources.
 */
class CogImageRep
{

	protected $filepath;
	protected $width;
	protected $height;
	protected $imageResource;
	protected $extension;

	public function __construct($filepath)
	{
		$this->filepath = $filepath;
		if (is_file($this->filepath))
		{
			$this->extension = CogFile::getExtension($this->filepath);
			list($this->width, $this->height) = getimagesize($this->filepath);
			$this->createImageResource();
		}
	}

	/**
	 * Creates a new image resource referenced by a CogImageRep instance which
	 * is returned.
	 *  
	 * @param string $filepath
	 * @param int $width
	 * @param int $height
	 * @return \CogImageRep The CogImageRep
	 */
	public static function createNew($filepath, $width, $height)
	{
		$imRep = new CogImageRep($filepath);
		$imRep->setWidth($width);
		$imRep->setHeight($height);
		$imRep->imageResource = imagecreatetruecolor($width, $height);

		return $imRep;
	}

	private function createImageResource()
	{
		switch ($this->extension)
		{
			case 'jpg':
				$this->imageResource = imagecreatefromjpeg($this->filepath);
				break;
			case 'gif':
				$this->imageResource = imagecreatefromgif($this->filepath);
				break;
			case 'png':
				$this->imageResource = imagecreatefrompng($this->filepath);
				break;
			case 'bmp':
				$this->imageResource = imagecreatefromwbmp($this->filepath);
				break;
			default:
				//Assume it's a jpeg image..
				$this->imageResource = imagecreatefromjpeg($this->filepath);
				break;
		}
	}

	/**
	 * Gets the image resource for this instance.
	 * 
	 * @return \CogImageRep 
	 */
	public function getImageResource()
	{
		return $this->imageResource;
	}

	/**
	 * Sets the image width for this instance.
	 * 
	 * @param int $width 
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	 * Gets the image width set for this instance.
	 * 
	 * @return int 
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Sets the image height for this instance.
	 * 
	 * @param int $height 
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}

	/**
	 * Gets the image height set for this instance.
	 * 
	 * @return int 
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Gets the file extension for the image referenced by this instance.
	 * 
	 * @return string 
	 */
	public function getExtension()
	{
		if (!isset($this->extension))
		{
			if (is_file)
			{
				$this->extension = CogFile::getExtension($this->filepath);
			}
		}
		return $this->extension;
	}

	/**
	 * Gets the filepath set in the constructor for this instance.
	 * 
	 * @return string 
	 */
	public function getFilepath()
	{
		return $this->filepath;
	}

	/**
	 * Copy the image referenced by this CogImageRep instance to the image filepath 
	 * referenced by the CogImageRep instance specified.
	 * 
	 * @param \CogImageRep
	 */
	public function copyTo($imageRep)
	{
		/* @var $imageRep CogImageRep */
		$success = imagecopyresampled($imageRep->getImageResource(), $this->getImageResource(), 0, 0, 0, 0, $imageRep->getWidth(), $imageRep->getHeight(), $this->getWidth(), $this->getHeight());
		if ($success)
		{
			CogFile::delete($imageRep->getFilepath());
			if (!is_null($imageRep->getFilepath()))
			{
				if (imagejpeg($imageRep->getImageResource(), $imageRep->getFilepath()))
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Sends the raw image referenced by this CogImageRep instance to the output
	 * buffer. 
	 */
	public function sendToOutputStream()
	{
		//imagegd2($this->getImageResource());
		switch ($this->extension)
		{
			case 'jpg':
				imagejpeg($this->imageResource);
				break;
			case 'gif':
				imagegif($this->imageResource);
				break;
			case 'png':
				imagepng($this->imageResource);
				break;
			case 'bmp':
				imagewbmp($this->imageResource);
				break;
			default:
				//Assume it's a jpeg image..
				imagejpeg($this->imageResource);
				break;
		}
	}

	/**
	 * Copies the image referenced by this instance to the specified
	 * filepath.
	 * 
	 * @param string $filepath 
	 */
	public function copyToFile($filepath)
	{
		switch ($this->extension)
		{
			case 'jpg':
				imagejpeg($this->imageResource, $filepath);
				break;
			case 'gif':
				imagegif($this->imageResource, $filepath);
				break;
			case 'png':
				imagepng($this->imageResource, $filepath);
				break;
			case 'bmp':
				imagewbmp($this->imageResource, $filepath);
				break;
			default:
				//Assume it's a jpeg image..
				imagejpeg($this->imageResource, $filepath);
				break;
		}
	}
}

?>
