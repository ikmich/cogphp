<?php

class CogDateFormat
{

	/**
	 * ISO 8601 date (added in PHP 5). E.g: 2004-02-12T15:19:21+00:00.
	 * 
	 * @var string 
	 */
	public static $iso_8601_date = 'c';

	/**
	 * RFC 2822 formatted date. E.g: Thu, 21 Dec 2000 16:01:07 +0200.
	 * 
	 * @var string
	 */
	public static $rfc_2822_date = 'r';

	/**
	 * Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT).
	 * 
	 * @var string 
	 */
	public static $secs_from_epoch = 'U';

	public static function build($format = array())
	{
		$formatString = '';		
		if (!empty($format))
		{
			foreach ($format as $formatEntry)
			{
				$formatString .= $formatEntry;
			}
		}
		
		return $formatString;
	}

}

?>
