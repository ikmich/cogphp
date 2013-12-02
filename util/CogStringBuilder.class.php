<?php

class CogStringBuilder
{

	private $str = "";
	private static $nl = "\r\n";

	public function __construct($string = null)
	{
		if (is_string($string))
		{
			$this->str = $string;
		}
	}

	public static function setNewLineChar($string = null)
	{
		if (is_string($string))
		{
			self::$nl = $string;
		}
	}

	public function append($val)
	{
		if (isset($val))
		{
			$this->str .= $val;
		}
		return $this;
	}

	public function appendLn($val = null)
	{
		$this->str .= self::$nl;
		if (isset($val))
		{
			$this->str .= $val;
		}
		return $this;
	}

	public function build()
	{
		return $this->str;
	}

	public function toString()
	{
		return $this->str;
	}
}

?>
