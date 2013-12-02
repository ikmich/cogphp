<?php

/**
 * Utility for working with arrays.
 * @author Ikmich
 */
class CogArray
{

	/**
	 * Internal array object.
	 * @var array 
	 */
	private $array = array();

	public function __construct($data = null)
	{
		if (isset($data))
		{
			if (is_array($data))
			{
				$this->array = $data;
			}
			else
			{
				$this->array[] = $data;
			}
		}
	}

	/**
	 * Adds a value to the array.
	 * @param mixed $value
	 */
	public function add($value)
	{
		$this->array[] = $value;
	}

	/**
	 * Adds an associative value (with key and value) to the array.
	 * 
	 * @param string $key
	 * @param mixed $value
	 */
	public function addKey($key, $value)
	{
		$this->array[$key] = $value;
	}

	/**
	 * Returns the internal array used.
	 * @return array
	 */
	public function getArray()
	{
		return $this->array;
	}

	/**
	 * Gets the length of the array.
	 * @return number
	 */
	public function getLength()
	{
		return count($this->array);
	}

	/**
	 * Returns the value at the specified id. The id could be a numeric index
	 * or a string key.
	 * 
	 * @param index|string $id
	 * @return mixed|null
	 */
	public function get($id)
	{
		if (isset($this->array[$id]))
		{
			return $this->array[$id];
		}
		return null;
	}

	/**
	 * Sets the value at a key or an index.
	 * 
	 * @param numeric|string $id The index or string key depending on the type of the array.
	 * @param mixed $value
	 */
	public function set($id, $value)
	{
		if (isset($this->array[$id]))
		{
			$this->array[$id] = $value;
		}
	}

	/**
	 * Checks if array has a key.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function hasKey($key)
	{
		return isset($this->array[$key]);
	}

	/**
	 * Returns the first item.
	 * @return mixed
	 */
	public function getFirst()
	{
		return array_shift($this->array);
	}

	public function getLast()
	{
		return array_pop($this->array);
	}

	/**
	 * Lists the contents of an array as an indented html line separated string.
	 * 
	 * @param array $array
	 * @return string
	 */
	public static function listString($array, $style = false)
	{

		function processArray($array, $style)
		{
			static $unit = '';
			static $depth = 0;
			//..........................................
			foreach ($array as $key => $value)
			{
				$margin = 15 * $depth;
				$unit .= "<div style='margin-left: {$margin}px;'>";

				if (is_array($value))
				{
					if ($style)
					{
						$unit .= "<div style='float:left; padding:3px; border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px; background-color:#dedede; cursor:default; margin:1px 0px 2px 0px;'><b>{$key} </b></div>";
						$unit .= "<div style='clear:both;'></div>";
					}
					else
					{
						$unit .= "{$key} ";
					}
					$depth++;
					processArray($value, $style);
				}
				else
				{
					$unit .= "<div style='cursor:default;'>{$key} : {$value}</div>";
					$unit .= "</div>";
				}
			}
			$depth--;
			return $unit;
		}
		return processArray($array, $style);
	}
}

?>
