<?php

/**
 * Simple utility class for accessing global variables.
 */
class CogGlobal
{

	public static function getValue($var)
	{
		if (isset($GLOBALS[$var]))
		{
			return $GLOBALS[$var];
		}
		return null;
	}

	public static function set($var, $value)
	{
		$GLOBALS[$var] = $value;
	}
}

?>