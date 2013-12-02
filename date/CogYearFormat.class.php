<?php

class CogYearFormat
{
	/**
	 * Whether it's a leap year. 1 if it is a leap year, 0 otherwise. 
	 * 
	 * @var string 
	 */
	public static $leap_year_check = 'L';
	
	/**
	 * ISO-8601 year number. This has the same value as Y, 
	 * except that if the ISO week number (W) belongs to the previous or 
	 * next year, that year is used instead. (added in PHP 5.1.0).
	 * 
	 * @var string 
	 */
	public static $iso_8601_num = 'o';
	
	/**
	 * A full numeric representation of a year, 4 digits.
	 * 
	 * @var string 
	 */
	public static $full = 'Y';
	
	/**
	 * A two digit representation of a year.
	 * 
	 * @var string 
	 */
	public static $digit_2 = 'y';
}
?>
