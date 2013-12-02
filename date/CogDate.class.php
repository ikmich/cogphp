<?php

/**
 * Provides for easy date string formatting.
 */
class CogDate
{

	protected $dateString;
	protected static $PATTERN_YEAR = '(year|yr){1}';
	protected static $PATTERN_MONTH = '(mth|0mth|Month|month){1}';
	protected static $PATTERN_DAY = '(dd|0d|Day|day){1}';
	protected static $PATTERN_HOUR = '(hr|0hr|h24|0h24){1}';
	protected static $PATTERN_MINUTE = '(min|0min){1}';
	protected static $PATTERN_SECONDS = '(sec|0sec){1}';
	protected static $PATTERN_AM_PM = '(am_pm|AM_PM){1}';
	protected $datePattern = '';

	public function __construct($formatString = null)
	{
		$this->datePattern = $this->buildPattern(array(
			self::$PATTERN_YEAR,
			self::$PATTERN_MONTH,
			self::$PATTERN_DAY,
			self::$PATTERN_HOUR,
			self::$PATTERN_MINUTE,
			self::$PATTERN_SECONDS,
			self::$PATTERN_AM_PM
		));

		if (!isset($formatString))
		{
			$formatString = "year-0mth-0d 0hr:0min:0secAM_PM";
		}
		$this->parseFormat($formatString);
	}

	private function buildPattern($patterns = array())
	{
		if (isset($patterns) && !empty($patterns))
		{
			$this->datePattern = '<';
			for ($i = 0; $i < count($patterns); $i++)
			{
				$this->datePattern .= $patterns[$i];
				if ($i < count($patterns) - 1)
				{
					$this->datePattern .= '|';
				}
			}
			$this->datePattern .= '>U'; //ungreedy pattern matching.
			return $this->datePattern;
		}
	}

	private function parseFormat($formatString)
	{
		$matchResults = array();

		if (preg_match_all($this->datePattern, $formatString, $matchResults))
		{
			$matches = $matchResults[0];
			foreach ($matches as $match)
			{
				$dateValue = '';

				switch ($match)
				{
					case 'year':
						$dateValue = date('Y');
						break;

					case 'yr':
						$dateValue = date('y');
						break;

					case 'mth':
						$dateValue = date('n');
						break;

					case '0mth':
						$dateValue = date('m');
						break;

					case 'Month':
						$dateValue = date('F');
						break;

					case 'month':
						$dateValue = date('M');
						break;

					case 'dd':
						$dateValue = date('j');
						break;

					case '0d':
						$dateValue = date('d');
						break;

					case 'Day':
						$dateValue = date('l');
						break;

					case 'day':
						$dateValue = date('D');
						break;

					case 'hr':
						$dateValue = date('g');
						break;

					case '0hr':
						$dateValue = date('h');
						break;

					case 'h24':
						$dateValue = date('G');
						break;

					case '0h24':
						$dateValue = date('H');
						break;

					case 'min':
						$dateValue = date('i');
						break;

					case '0min':
						$dateValue = date('i');
						break;

					case 'sec':
						$dateValue = date('s');
						break;

					case '0sec':
						$dateValue = date('s');
						break;

					case 'am_pm':
						$dateValue = date('a');
						break;

					case 'AM_PM':
						$dateValue = date('A');
						break;
				}

				$formatString = preg_replace('<' . $match . '>', $dateValue, $formatString);
			}

			$this->dateString = $formatString;
		}
		//else: no matches.
	}

	/**
	 * Overrides the default __toString() function.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->dateString;
	}

	/**
	 * Prints out the date value format options used by this class.
	 */
	public static function printFormatOptions()
	{
		$options = array(
			'year' => 'Full year',
			'yr' => 'Short year',
			'mth' => 'Month number without leading zero.',
			'0mth' => 'Month number with leading zero.',
			'Month' => 'Full month name ',
			'month' => 'Shortened month name',
			'dd' => 'Day number without leading zero',
			'0d' => 'Day number with leading zero',
			'Day' => 'Full day name',
			'day' => 'Shortened day name',
			'hr' => '12-hour value without leading zero',
			'0hr' => '12-hour value with leading zero',
			'h24' => '24-hour value without leading zero',
			'0h24' => '24-hour value with leading zero',
			'min' => 'Minute value without leading zero',
			'0min' => 'Minute value with leading zero',
			'sec' => 'Seconds value without leading zero',
			'0sec' => 'Seconds value with leading zero',
			'am_pm' => 'Lowercase "a.m" or "p.m" string',
			'AM_PM' => 'Uppercase "A.M" OR "P.M" string'
		);

		$output = '';

		$sb = new CogStringBuilder('<script>');
		$sb->append('function CogDateSampleTable_mouseover(cell){');
		$sb->append('var color = "#f6f6f6";');
		$sb->append('cell.style.backgroundColor = color;');
		$sb->append('if(cell.nextSibling) cell.nextSibling.style.backgroundColor = color;');
		$sb->append('if(cell.previousSibling) cell.previousSibling.style.backgroundColor = color;');
		$sb->append('}');
		$sb->append('function CogDateSampleTable_mouseout(cell){');
		$sb->append('cell.style.background = "none";');
		$sb->append('if(cell.nextSibling) cell.nextSibling.style.background = "none";');
		$sb->append('if(cell.previousSibling) cell.previousSibling.style.background = "none";');
		$sb->append('}');
		$sb->append('</script>');

		$output .= $sb->toString();
		$output .= '<br /><table cellpadding="5" style="margin: 20px 0px 20px 0px;width: 100%; border-collapse: collapse; cursor: default;">';
		foreach ($options as $format => $description)
		{
			$output .= '<tr>';
			$output .= '<td style="border: 1px solid #ededed;" onmouseover=\'CogDateSampleTable_mouseover(this);\' onmouseout=\'CogDateSampleTable_mouseout(this);\'>' . $format . '</td>';
			$output .= '<td style="border: 1px solid #ededed;" onmouseover=\'CogDateSampleTable_mouseover(this);\' onmouseout=\'CogDateSampleTable_mouseout(this);\'>' . $description . '</td>';
			$output .= '</tr>';
		}
		$output .= '</table><br />';
		print $output;
	}
}

?>
