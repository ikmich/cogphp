<?php

class CogTimezoneFormat
{

	/**
	 * Timezone identifier (added in PHP 5.1.0).
	 * 
	 * @var string
	 */
	public static $id = 'e';

	/**
	 * Whether or not the date is in daylight saving time.
	 * 
	 * @var string
	 */
	public static $daylight_savings_check = 'I';

	/**
	 * Difference to Greenwich time (GMT) in hours. E.g: +0200.
	 * 
	 * @var string
	 */
	public static $gmt_diff = 'O';

	/**
	 * Difference to Greenwich time (GMT) with colon between hours and 
	 * minutes (added in PHP 5.1.3). E.g: +02:00
	 * 
	 * @var string
	 */
	public static $gmt_diff_colon = 'P';

	/**
	 * Timezone abbreviation. E.g: EST, MDT.
	 * 
	 * @var string
	 */
	public static $abbrev = 'T';

	/**
	 * Timezone offset in seconds. The offset for timezones west of UTC is 
	 * always negative, and for those east of UTC is always positive.
	 * 
	 * @var string 
	 */
	public static $offset_secs = 'Z';

}

?>
