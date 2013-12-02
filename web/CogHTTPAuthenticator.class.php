<?php

/**
 * Utility class for HTTP Authentication.
 */
class CogHTTPAuthenticator
{

	private static $username;
	private static $password;

	//private static $authenticated = false;

	/**
	 * Registers a username and password for authentication.
	 * 
	 * @param string $user
	 * @param string $password
	 */
	public static function register($user, $password)
	{
		self::$username = $user;
		self::$password = $password;
	}

	/**
	 * Makes a login request to the user.
	 */
	public static function requestLogin()
	{
		if (!self::isLoggedIn())
		{
			header("WWW-Authenticate: Basic realm='Restricted'");
			header("HTTP/1.1 401 Unauthorized");
		}
	}

	/**
	 * Checks if the user is logged in.
	 * 
	 * @return boolean
	 */
	public static function isLoggedIn()
	{
		if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
		{
			if (self::$username === $_SERVER['PHP_AUTH_USER'] && self::$password === $_SERVER['PHP_AUTH_PW'])
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Logs the user out.
	 */
	public static function logout()
	{
		unset($_SERVER['PHP_AUTH_USER']);
		unset($_SERVER['PHP_AUTH_PW']);
	}

}

?>
