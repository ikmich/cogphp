<?php

class CogCSS
{

	/**
	 * Adds a html 'link' tag with rel='stylesheet' and type='text/css' attributes.
	 * 
	 * @param type $cssRef A string url representing one CSS file to link, or an
	 * array specifying a number urls of CSS files to be linked.
	 */
	public static function link($cssRef)
	{
		self::ref($cssRef);
	}

	/**
	 * Adds a html 'link' tag with rel='stylesheet' and type='text/css' attributes.
	 * 
	 * @param type $cssRef A string url representing one CSS file to link, or an
	 * array specifying a number urls of CSS files to be linked.
	 */
	public static function ref($cssRef)
	{
		if (isset($cssRef) && !empty($cssRef))
		{
			$urls = array();
			if (gettype($cssRef) == 'string')
			{
				$urls[] = $cssRef;
			}
			else
			{
				$urls = $cssRef;
			}

			if (is_array($urls))
			{
				foreach ($urls as $cssRef)
				{
					?>
					<link rel="stylesheet" type="text/css" href="<?php print $cssRef; ?>" />
					<?php
				}
			}
		}
	}
}
?>
