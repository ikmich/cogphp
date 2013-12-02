<?php

/**
 * Provides utility methods for working with a http request.
 */
class CogRequest
{

	public static function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Check if the request is a POST. 
	 * @return boolean
	 */
	public static function isPost()
	{
		return strtolower(self::getMethod()) === 'post';
	}

	/**
	 * Checks if the request is a GET.
	 * @return boolean
	 */
	public static function isGet()
	{
		return strtolower(self::getMethod()) === 'get';
	}

	/**
	 * Checks if the request has a particular POST variable, or if that
	 * POST variable is equal to a value if specified.
	 * 
	 * @param string $name The name of the POST var.
	 * @param string $value Optional. The value to compare to if supplied.
	 * @return boolean
	 */
	public static function hasPOST($name = null, $value = null)
	{
		if (isset($name))
		{
			//Check if has post var $var.
			if (isset($_POST[$name]) && !empty($_POST[$name]))
			{
				if (isset($value))
				{
					return $_POST[$name] === $value;
				}
				return true;
			}
			return false;
		}
		else
		{
			//Check if has post vars at all.
			return !empty($_POST);
		}
	}

	/**
	 * Gets the value of the POST variable with the name specified.
	 * 
	 * @param string $var
	 * @return string
	 */
	public static function retrievePOST($var)
	{
		return htmlspecialchars(strip_tags($_POST[$var]));
	}

	/**
	 * Checks if the request has a particular GET variable, or if that
	 * GET variable is equal to a value if specified.
	 * 
	 * @param string $var The name of the GET var.
	 * @param string $value Optional. The value to compare to if supplied.
	 * @return boolean
	 */
	public static function hasGET($var = null, $value = null)
	{
		if (isset($var))
		{
			if (isset($_GET[$var]) && !empty($_GET[$var]))
			{
				if (isset($value))
				{
					return $_GET[$var] == $value;
				}
				return true;
			}
			return false;
		}
		else
		{
			//Check if has any get vars
			return !empty($_GET);
		}
	}

	/**
	 * Retrieve a GET request parameter.
	 * @param string $var
	 * @return string
	 */
	public static function retrieveGET($var)
	{
		//return htmlspecialchars(strip_tags($_GET[$var]));
		return $_GET[$var];
	}

	/**
	 * Checks if the request is a FILE request.
	 * @return boolean
	 */
	public static function isFile()
	{
		if (!empty($_FILES))
		{
			return true;
		}
		return false;
	}

	/**
	 * Retrieve the reference to a FILE upload request.
	 * @param string $var The upload name.
	 * @return mixed
	 */
	public static function retrieveFILE($var)
	{
		return $_FILES[$var];
	}

	/**
	 * Checks if a FILE upload request was sent.
	 * @param string $uploadName
	 * @return boolean
	 */
	public static function hasFile($uploadName = null)
	{
		if (isset($uploadName))
		{
			return CogUploadUtil::fileUploaded($uploadName);
		}
		return !empty($_FILES);
	}
}

?>