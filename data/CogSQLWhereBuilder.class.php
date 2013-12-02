<?php

/**
 * Utility class to help build a sql where clause
 */
class CogSQLWhereBuilder
{

	private $fullClause = '';
	private $conditions = array();
	private $operators = array();

	/**
	 * Parses a field value to determine whether a string value should
	 * be automatically quoted for db operations.
	 * 
	 * @param string $value
	 * @return string The parsed value.
	 */
	private function quotifyValueForQuery($value)
	{
		return CogDbClient::quotifyForQuery($value);
	}

	public function equals($key, $value)
	{
		if (isset($value))
		{
			$this->conditions[] = "{$key} = {$this->quotifyValueForQuery($value)}";
		}
		else
		{
			$this->conditions[] = "{$key} is null";
		}

		return $this;
	}
	
	public function notEquals($key, $value) {
		if (isset($value))
		{
			$this->conditions[] = "{$key} != {$this->quotifyValueForQuery($value)}";
		}
		else
		{
			$this->conditions[] = "{$key} is not null";
		}

		return $this;
	}

	public function lessThan($key, $value)
	{
		$this->conditions[] = "{$key} < {$this->quotifyValueForQuery($value)}";
		return $this;
	}

	public function greaterThan($key, $value)
	{
		$this->conditions[] = "{$key} > {$this->quotifyValueForQuery($value)}";
		return $this;
	}

	public function lessThanOrEquals($key, $value)
	{
		$this->conditions[] = "{$key} <= {$this->quotifyValueForQuery($value)}";
		return $this;
	}

	public function greaterThanOrEquals($key, $value)
	{
		$this->conditions[] = "{$key} >= {$this->quotifyValueForQuery($value)}";
		return $this;
	}

	public function like($field, $comparison)
	{
		$this->conditions[] = "{$field} LIKE {$this->quotifyValueForQuery($comparison)}";
		return $this;
	}

	public function _and()
	{
		$this->operators[] = ' AND ';
		return $this;
	}

	public function _or()
	{
		$this->operators[] = ' OR ';
		return $this;
	}

	public function build()
	{
		for ($i = 0; $i < count($this->conditions); $i++)
		{
			$this->fullClause .= $this->conditions[$i];
			if ($i < count($this->conditions) - 1)
			{
				$this->fullClause .= $this->operators[$i];
			}
		}

		//collapse multiple whitespace to one
		$this->fullClause = trim(preg_replace('<\s+>', ' ', $this->fullClause));

		return $this->fullClause;
	}
}

?>
