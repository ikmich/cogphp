<?php

class CogDayFormat
{

	/**
	 * English ordinal suffix for the day of the month, 2 characters.
	 * 
	 * @var string 
	 */
	public static $ordinal_suffix = 'S';

	/**
	 * Day of the month, without leading zeros for values below 10.
	 * 
	 * @var string 
	 */
	public static $num = 'j';
	public static $num_ordinal = 'jS';

	/**
	 * Day of the month, with leading zeros for values below 10.
	 * 
	 * @var string 
	 */
	public static $num_0 = 'd';
	public static $num_0_ordinal = 'dS';

	/**
	 * Textual representation of a day, three letters.
	 * 
	 * @var string
	 */
	public static $string_3 = 'D';

	/**
	 * Textual representation of a day, three letters.
	 * 
	 * @var string
	 */
	public static $short = 'D';

	/**
	 * Full textual representation of the day of the week.
	 * 
	 * @var string 
	 */
	public static $full = 'l';

	/**
	 * ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0).
	 * 
	 * @var string 
	 */
	public static $iso_8601_num = 'N';

	/**
	 * ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)
	 * with ordinal suffix.
	 * 
	 * @var string 
	 */
	public static $iso_8601_num_suffix = 'NS';

	/**
	 * Numeric representation of the day of the week.
	 * 
	 * @var string
	 */
	public static $num_day_of_week = 'w';
	public static $num_day_of_week_ordinal = 'wS';

	/**
	 * The day of the year (starting from 0).
	 * 
	 * @var string 
	 */
	public static $num_day_of_year = 'z';
	public static $num_day_of_year_ordinal = 'zS';

}

?>
