<?php

function fn_load()
{
	//This script will have a maximum of 180 seconds (3 minutes) to execute before a timeout is issued.
	set_time_limit(180);

	//If load.php has been called before, don't bother with it anymore.
	if (function_exists("cogAutoLoad"))
	{
		return;
	}

	//Create the main custom autoload function...
	function cogAutoLoad($classname)
	{
		//Get the directory path of the folder that contains the Cog library
		$libfolderPath = dirname(__FILE__); // . "/lib";
		seekAndAct($libfolderPath, $classname);
	}
	/* Check if the magic function __autoload has already been declared elsewhere. if so,
	 * register it with spl_autoload_register()... */
	if (function_exists("__autoload"))
	{
		spl_autoload_register("__autoload");
	}

	//Register the main custom autoload function...
	spl_autoload_register("cogAutoLoad");

	//-------------------------------------------------------------------------------

	function seekAndAct($folderpath, $nameOfClass)
	{
		$filenameOfClass = "{$nameOfClass}.class.php";
		$filenameOfInterface = "{$nameOfClass}.interface.php";
		$folderContents = scandir($folderpath);

		foreach ($folderContents as $item)
		{
			//Eliminate those default "." and ".." folder entries
			if ($item != "." && $item != "..")
			{
				$itemPath = "{$folderpath}/{$item}";

				if (is_file($itemPath))
				{
					if ($item == $filenameOfClass)
					{
						//Include the class, and break out.
						$filepathOfClass = "{$folderpath}/{$filenameOfClass}";
						@require_once "{$filepathOfClass}";
						break;
					}

					if ($item == $filenameOfInterface)
					{
						//Include the interface and break out.
						$filepathOfInterface = "{$folderpath}/{$filenameOfInterface}";
						@require_once "{$filepathOfInterface}";
						break;
					}
				}

				if (is_dir($itemPath))
				{
					//A folder. Search it again, recursively.
					seekAndAct($itemPath, $nameOfClass);
				}
			}
		}
	}
}
/*
 * Make $_root, $_base and $_home available..
 */
//	global $_root;
//	global $_home;
//	global $_base;

if (!isset($_base))
{
	$_base = 'http://' . @$_SERVER['HTTP_HOST'];
}

if (!isset($_root))
{
	$_root = @$_SERVER['DOCUMENT_ROOT'];
}

if (!isset($_home))
{
	$_home = dirname($_root);
}

while (strtolower(basename($_home)) == "public_html" || strtolower(basename($_home)) == "www")
{
	$_home = dirname($_home);
}

fn_load();
?>