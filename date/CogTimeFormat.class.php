<?php

class CogTimeFormat
{

	public static $am_pm_lower = 'a';
	public static $am_pm_upper = 'A';
	public static $swatch_internet_time = 'B';

	/**
	 * 12-hour format of an hour without leading zeros.
	 * 
	 * @var string 
	 */
	public static $hour_12 = 'g';

	/**
	 * 12-hour format of an hour with leading zeros.
	 * 
	 * @var string 
	 */
	public static $hour_12_0 = 'h';

	/**
	 * 24-hour format of an hour without leading zeros.
	 * 
	 * @var string 
	 */
	public static $hour_24 = 'G';

	/**
	 * 24-hour format of an hour with leading zeros.
	 * 
	 * @var string 
	 */
	public static $hour_24_0 = 'H';

	/**
	 * Minutes with leading zeros.
	 * 
	 * @var string 
	 */
	public static $mins_0 = 'i';

	/**
	 * Seconds with leading zeros.
	 * 
	 * @var string 
	 */
	public static $secs_0 = 's';

	/**
	 * Milliseconds (added in PHP 5.2.2).
	 * 
	 * @var string
	 */
	public static $millis = 'u';

}

?>
