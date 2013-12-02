<?php

class Cog
{

	/**
	 * Outputs one or more HTML line-breaks, depending on the argument passed or none.
	 * 
	 * @param integer $num Optional number of line breaks to print. If none is
	 * passed, a single line break is printed.
	 * 
	 */
	public static function br()
	{
		$numArgs = @func_num_args();
		$arg = @func_get_arg(0);
		$s = "";

		if ($numArgs > 0 && $arg > 0)
		{
			for ($i = 0; $i < $arg; $i++)
			{
				$s .= "<br />";
			}
		}
		else
		{
			$s = "<br />";
		}

		echo($s);
	}

	/**
	 * Outputs one or more newlines, depending on the argument passed or none.
	 */
	public static function nl()
	{
		$numArgs = @func_num_args();
		$arg = @func_get_arg(0);
		$s = "";

		if ($numArgs > 0 && $arg > 0)
		{
			for ($i = 0; $i < $arg; $i++)
			{
				$s .= "\r\n";
			}
		}
		else
		{
			$s = "\r\n";
		}
		echo($s);
	}

	/**
	 * Outputs a string HTML-inline.
	 */
	public static function printIn($string)
	{
		print "<span style='font-family: consolas, monospace; font-size: 14px; color: #050505;'>{$string}</span>";
	}

	public static function printInColor($string, $color)
	{
		self::printIn("<span style='color:{$color};'>{$string}</span>");
	}

	/**
	 * Same as printIn(), but in red color.
	 */
	public static function printInRed($string)
	{
		self::printInColor($string, '#cc0000');
	}

	/**
	 * Same as printIn(), but in green color.
	 */
	public static function printInGreen($string)
	{
		self::printInColor($string, '#99cc33');
	}

	/**
	 * Same as printIn(), but in blue color.
	 */
	public static function printInBlue($string)
	{
		self::printInColor($string, '#006699');
	}

	/**
	 * Same as printIn(), but in orange color.
	 */
	public static function printInOrange($string)
	{
		self::printInColor($string, 'orange');
	}

	public static function printInPurple($string)
	{
		self::printInColor($string, '#663366');
	}

	/**
	 * Outputs a string on a new HTML line.
	 */
	public static function printLn($string, $color = null)
	{
		print "<br />";
		if (is_null($color))
		{
			$string = @func_get_arg(0);
			self::printIn($string);
		}
		else
		{
			$string = @func_get_arg(0);
			$color = @func_get_arg(1);
			self::printInColor($string, $color);
		}
	}

	public static function printLnColor($string, $color)
	{
		self::printLn($string, $color);
	}

	/**
	 * Same as printLn(), but in red color.
	 */
	public static function printLnRed($string)
	{
		self::printLn("<span style='color:#cc0000;'>{$string}</span>");
	}

	/**
	 * Same as printLn(), but in green color.
	 */
	public static function printLnGreen($string)
	{
		self::printLnColor($string, '#99cc66');
	}

	/**
	 * Same as printLn(), but in blue color.
	 */
	public static function printLnBlue($string)
	{
		self::printLnColor($string, '#006699');
	}

	/**
	 * Same as printLn(), but in orange color.
	 */
	public static function printLnOrange($string)
	{
		self::printLnColor($string, 'orange');
	}

	public static function printLnPurple($string)
	{
		print "<br />";
		self::printInColor($string, '#663366');
	}

	public static function printLnx($string)
	{
		self::printLn($string);
		print "<br />";
	}

	/**
	 * Same as printLnx() but in red color.
	 */
	public static function printLnxRed($string)
	{
		self::printLnColor($string, '#cc0000');
		print "<br />";
	}

	/**
	 * Same as printLnx() but in green color.
	 */
	public static function printLnxGreen($string)
	{
		self::printLnColor($string, '#99cc66');
		print "<br />";
	}

	/**
	 * Same as printLnx() but in blue color.
	 */
	public static function printLnxBlue($string)
	{
		self::printLnColor($string, '#006699');
		print "<br />";
	}

	/**
	 * Same as printLnx() but in orange color.
	 */
	public static function printLnxOrange($string)
	{
		self::printLnColor($string, '#ff9900');
		print "<br />";
	}

	public static function printLnxPurple($string)
	{
		self::printLnColor($string, '#663366');
		print "<br />";
	}

	public static function errorNotice($msg, $fatal = false)
	{
		self::printNotice("ERROR! " . $msg, "#cc0000", $fatal);
	}

	public static function successNotice($msg, $fatal = false)
	{
		self::printNotice($msg, "#669933", $fatal);
	}

	public static function infoNotice($msg, $fatal = false)
	{
		self::printNotice($msg, "#529AC5", $fatal);
	}

	public static function warningNotice($msg, $fatal = false)
	{
		self::printNotice($msg, "#FAD172", $fatal, "#030303", false);
	}

	private static function printNotice($msg, $bgColor, $fatal = false, $fontColor = null, $use_text_shadow = true)
	{
		if (!isset($fontColor))
		{
			$fontColor = "#ffffff";
		}

		if ($use_text_shadow)
		{
			$textShadowValue = "0px 1px 0px #040404";
		}
		else
		{
			$textShadowValue = "none";
		}

		$msg = "<div style='clear:both;'></div>
		<div style='color: {$fontColor}; font-weight: bold; font-family: sans-serif; font-size: 11px; text-shadow: {$textShadowValue}; cursor: default; float: left; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; padding: 6px 7px 5px 7px; background-color: {$bgColor}; '>{$msg}</div>
			<div style='clear:both; margin-bottom: 2px;'></div>";

		if ($fatal)
		{
			die($msg);
		}
		else
		{
			print $msg;
		}
	}

	public static function browserCheck()
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];

		if (preg_match("/MSIE/", $ua))
		{
			return "ie";
		}
		else if (preg_match("/Firefox/", $ua))
		{
			return "firefox";
		}
		else if (preg_match("/Chrome/", $ua))
		{
			return "chrome";
		}
		else if (preg_match("/Gecko/", $ua))
		{
			return "gecko";
		}
		//do checks for other browsers
	}

	public static function osCheck()
	{
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/windows/i', $ua))
		{
			return "windows";
		}
		else if (preg_match('/linux/', $ua))
		{
			return "linux";
		}
		else
		{
			return "unknown";
		}
	}

	public static function prepareTextForDb($text)
	{
		return htmlspecialchars(addslashes(trim($text)));
	}

	public static function prepareTextFromDb($text)
	{
		return stripslashes($text);
	}

	public static function cleanTextForFileUploadName($text)
	{
		return self::prepareTextForFileUploadName($text);
	}

	public static function prepareTextForFileUploadName($text)
	{
		$text = stripslashes($text);
		return $text;
	}

	/**
	 * Returns TRUE if an email address has valid syntax, or FALSE otherwise.
	 * @param string $email
	 * @return boolean
	 */
	public static function validate_email($email)
	{
		if (is_string($email) && !empty($email))
		{
			//$rex = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
			$rex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/i"; //Case-insensitve.
			if (preg_match($rex, $email))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns TRUE if a phone number has valid syntax, or FALSE otherwise.
	 * @param mixed $phone A <b>string</b> or an <b>integer</b>
	 * @return boolean
	 */
	public static function validate_phone($phone)
	{
		if ($phone !== null && $phone !== "")
		{
			$rex = "<^[0-9][0-9\-\s]+[0-9]$>";
			if (preg_match($rex, $phone))
			{
				return true;
			}
		}
		return false;
	}

	public static function parseQuotes($string)
	{
		$string = preg_replace('<\">', "\\" . '"', $string);
		$string2 = preg_replace("<\'>", "\\" . "'", $string);
		return $string2;
	}

	public static function redirect($toPath)
	{
		header("Location:{$toPath}");
	}

	/**
	 * Same as Cog::isdef()
	 * @param mixed $args
	 * @return boolean
	 */
	public static function isdefined()
	{
		return self::isdef(@func_get_args());
	}

	/**
	 * Tests if a variable or all of a comma-separated set of variables exists.
	 * @param mixed $args $args could be either one argument or multiple arguments.
	 * When more than one, Cog::isdef() tests all of them, and returns FALSE if just one
	 * of them is not defined.
	 * @return bool
	 */
	public static function isdef()
	{
		$numArgs = @func_num_args();

		if ($numArgs === 1)
		{
			$var = @func_get_arg(0);
			if ($var !== null)
			{
				return true;
			}
			return false;
		}
		else if ($numArgs > 1)
		{
			$flag = true;
			for ($i = 0; $i < $numArgs; $i++)
			{
				$var = @func_get_arg($i);
				if ($var !== null)
				{
					continue;
				}
				else
				{
					$flag = false;
					break;
				}
			}
			return $flag;
		}
	}

	/**
	 * Loads all classes in the specified path when the classes are used.
	 * 
	 * @param classpath <p>The path to the directory where the classes are located.
	 * A class file in the classpath should contain just the one class it defines.
	 * The class file naming convention should be: Classname.class.php, where Classname is
	 * the name of the class as declared in the class file.</p>
	 */
	public static function classpath($classpath)
	{
		//resolve the path
		$GLOBALS['autoload_classpath'] = $classpath;

		//register the custom autoload function unless it previously exists (has been registered before)...
		if (function_exists("cogLoadClasspath"))
		{
			return;
		}

		function cogLoadClasspath($classname)
		{
			fn_loadClasspath($GLOBALS["autoload_classpath"], $classname);
		}
		spl_autoload_register("cogLoadClasspath");

		//do the class loading...
		function fn_loadClasspath($folderpath, $nameOfClass)
		{
			$filenameOfClass = "{$nameOfClass}.class.php";
			$filenameOfInterface = "{$nameOfClass}.interface.php";
			$folderContents = scandir($folderpath);

			foreach ($folderContents as $folderName)
			{
				//Eliminate those default "." and ".." folder entries
				if ($folderName != "." && $folderName != "..")
				{
					$contentPath = "{$folderpath}/{$folderName}";
					if (is_file($contentPath))
					{
						if ($folderName == $filenameOfClass)
						{
							//include this class and break out..
							$filepathOfClass = "{$folderpath}/{$filenameOfClass}";
							@require_once "{$filepathOfClass}";
							break;
						}
						else if ($folderName == $filenameOfInterface)
						{
							//include this interface and break out..
							$filepathOfInterface = "{$folderpath}/{$filenameOfInterface}";
							@require_once "{$filepathOfInterface}";
							break;
						}
					}
					if (is_dir($contentPath))
					{
						//A folder. Search it again, recursively.
						fn_loadClasspath($contentPath, $nameOfClass);
					}
				}
			}
		}
	}

	public static function includeFile($path)
	{
		require_once $path;
	}

	/**
	 * Includes all files in the specified folder path. It is not
	 * recursive.
	 * 
	 * @param string $dirpath <p>The path to the folder to include</p>
	 */
	public static function includePath($dirpath, array $names = null)
	{
		$dirpath = CogFS::normalize($dirpath);
		if (is_dir($dirpath))
		{
			if (is_null($names))
			{
				$contents = CogDir::getContents($dirpath);
				foreach ($contents as $content)
				{
					$contentPath = "{$dirpath}/{$content}";
					if (is_file($contentPath))
					{
						@require_once($contentPath);
					}
				}
			}
			else
			{
				foreach ($names as $filename)
				{
					$filepath = $dirpath . '/' . $filename;
					if (is_file($filepath))
					{
						@require_once($filepath);
					}
				}
			}
		}
	}

	/**
	 * WARNING!!! Not for public/production use.
	 * Used to include css and js references to "base-ui" files developed
	 * locally by Ikenna Michael.
	 */
	public static function includeBaseUIRefs()
	{
		CogCSS::link('http://labs:8080/base-ui/base-ui.css');
		CogJS::src('http://labs:8080/resources/js/jquery-1.8.0.min.js');
		CogJS::src('http://labs:8080/base-ui/base-ui.css.js');
		CogJS::src('http://cogjs-project:8080/cogjs/cog.extension.js');
	}

	public static function isVoid($var)
	{
		return !isset($var) || empty($var);
	}

	public static function beep()
	{
		$dir = dirname(__FILE__);
		$cmd = $dir . '/beep.wav';
		try
		{
			shell_exec($cmd);
		}
		catch (Exception $ex)
		{
			die("Error: " . $ex->getMessage());
		}
	}

	/**
	 * Prints out a string to indicate that the CogPHP framework is working.
	 * Use this to test your Cog setup.
	 * 
	 * WARNING!!! Not for public/production use.
	 */
	public static function test()
	{
		print "<br/>[ Testing... <br/> CogPHP is working!... ]<br/>";
	}
}

//end class Cog
?>