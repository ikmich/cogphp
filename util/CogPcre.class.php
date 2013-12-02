<?php

/**
 * Utility class that helps with the creation of regular expression patterns.
 */
class CogPcre
{

	public $description = "";
	private $fullPattern = "";
	private $delimiter = "/";
	private $inlinePattern = "";
	private $built = false;

	//constructor
	public function __construct()
	{
		
	}

	public function setDelimiter($delimiter)
	{
		if (is_string($delimiter)) {
			$this->delimiter = $delimiter;
		}
		return $this;
	}

	public function add($pattern)
	{
		if (is_string($pattern)) {
			$this->inlinePattern .= $pattern;
		}
		return $this;
	}

	/**
	 * Builds the regular expression pattern.
	 * 
	 * @param boolean $is_case_sensitive (Optional). Default: true.
	 */
	public function build($case_sensitive = FALSE)
	{
		if ($case_sensitive) {
			$this->fullPattern = $this->delimiter . $this->inlinePattern . $this->delimiter;
		}
		else {
			$this->fullPattern = $this->delimiter . $this->inlinePattern . $this->delimiter . "i";
		}

		$this->built = true;
	}

	public function toString()
	{
		return $this->inlinePattern;
	}

	public function getPcre()
	{
		if ($this->built) {
			return $this->fullPattern;
		}
		else {
			return null;
		}
	}
}

?>