<?php

/**
 * Utility class for working with a PHP web page.
 */
class CogPage
{

	/**
	 * Returns the current page being run.
	 * 
	 * @return string
	 */
	public static function this()
	{
		return $_SERVER['PHP_SELF'];
	}

	/**
	 * Same as CogPage::this().
	 * 
	 * @return string
	 */
	public static function self()
	{
	return self::this();




	}

/**
 * Loads the page using javascript. Tries to mimic hitting enter on the browser address bar.
 * Forms are not resubmitted.
 */
public static function load()
{
	?>
	<script type="text/javascript">
		var target = location.href;
		location.href = target;
	</script>
	<?php
}

/**
 * Reloads/refreshes the page using javascript. Forms will be resubmitted.
 */
public static function reload()
{
	?>
	<script type="text/javascript">
		location.reload();
	</script>
	<?php
}
}
?>