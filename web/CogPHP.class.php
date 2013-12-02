<?php

/**
 * Utility class for working with the PHP environment.
 */
class CogPHP
{

	/**
	 * Displays info about the php setup.
	 */
	public static function info()
	{
		phpinfo();
	}

	/**
	 * Returns the php version running.
	 */
	public static function getVersion()
	{
		//gets the version of php running.
		return phpversion();
	}

	public static function getUser()
	{
		return get_current_user();
	}

	/**
	 * Returns the loaded extensions in the current runtime.
	 * 
	 * @return array
	 */
	public static function getModules()
	{
		return get_loaded_extensions();
	}

	/**
	 * Tests if a PHP extension module is loaded.
	 * 
	 * @param string $module
	 * @return boolean
	 */
	public static function moduleIsLoaded($module)
	{
		return extension_loaded($module);
	}

	/**
	 * Returns all the functions defined within a PHP extension module.
	 * 
	 * @param string $module
	 * @return mixed Array of all functions; FALSE if module is not a valid extension.
	 */
	public static function getModuleFunctions($module)
	{
		return get_extension_funcs($module);
	}

	/**
	 * Gets the value of a PHP config option setting.
	 * 
	 * @param string $configOption
	 * @return mixed
	 */
	public static function getConfigOption($configOption)
	{
		$ret = ini_get($configOption);
		return $ret;
	}

	/**
	 * Sets a PHP configuration option.
	 * 
	 * @param string $configOption
	 * @param mixed $val
	 * @return mixed The old value on success; FALSE on failure.
	 */
	public static function setConfigOption($configOption, $val)
	{
		return ini_set($configOption, $val);
	}

	/**
	 * Returns all PHP configuration options.
	 * 
	 * @return array Associative array with keys as the option names.
	 */
	public static function getAllOptions()
	{
		return ini_get_all();
	}
}

?>
