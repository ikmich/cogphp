<?php

class CogMonthFormat
{

	/**
	 * A full textual representation of a month, such as January or March.
	 * 
	 * @var string
	 */
	public static $full = 'F';

	/**
	 * Numeric representation of a month, with leading zeros. 01 - 12
	 * 
	 * @var string
	 */
	public static $num_0 = 'm';

	/**
	 * Numeric representation of a month, without leading zeros.
	 * 
	 * @var string
	 */
	public static $num = 'n';

	/**
	 * A short textual representation of a month, three letters.
	 * 
	 * @var string
	 */
	public static $short = 'M';
	public static $string_3 = 'M';

	/**
	 * Number of days in the given month.
	 * 
	 * @var string 
	 */
	public static $num_days = 't';

}

?>
