<?php

/**
 * Utility class for performing operations with javascript.
 */
class CogJS
{

	/**
	 * Creates a browser 'alert' dialog.
	 * 
	 * @param string $message The message that will be displayed in the alert dialog.
	 */
	public static function alert($message)
	{
		?>
		<script type="text/javascript">
			alert("<?php print $message; ?>");
		</script>
		<?php
	}

	/**
	 * Creates a browser 'confirm' dialog.
	 * 
	 * @param string $message The message that will be displayed in the confirm dialog.
	 */
	public static function confirm($message)
	{
		?>
		<script type="text/javascript">
			confirm("<?php print $message; ?>");
		</script>
		<?php
	}

	/**
	 * Redirects the browser to a new URL.
	 * 
	 * @param string $url The new URL to navigate to.
	 */
	public static function redirect($url)
	{
		if (isset($url) && !empty($url))
		{
			?>
			<script type="text/javascript">
				location.href = "<?php print $url; ?>";
			</script>
			<?php
		}
	}

	/**
	 * Navigates backward in the browser history.
	 */
	public static function back()
	{
		?>
		<script type="text/javascript">
			history.back();
		</script>
		<?php
	}

	/**
	 * Navigates forward in the browser history.
	 */
	public static function forward()
	{
		?>
		<script type="text/javascript">
			history.forward();
		</script>
		<?php
	}

	/**
	 * Loads a page again. Does not resubmit form data.
	 */
	public static function reload()
	{
		?>
		<script type="text/javascript">
			var target = location.href;
			location.href = target;
		</script>
		<?php
	}

	/**
	 * Refreshes a page. Form data will be resubmitted.
	 */
	public static function refresh()
	{
		?>
		<script type="text/javascript">
			location.reload();
		</script>
		<?php
	}

	/**
	 * Outputs a HTML external javascript reference <script> tag.
	 * 
	 * @param string $jsRef A string url representing one javascript file to be
	 * referenced, or an array specifying a number of urls of javascript files to be
	 * referenced.
	 */
	public static function src($jsRef)
	{
		if (isset($jsRef) && !empty($jsRef))
		{
			$urls = array();
			if (gettype($jsRef) == 'string')
			{
				$urls[] = $jsRef;
			}
			else
			{
				$urls = $jsRef;
			}

			if (is_array($urls))
			{
				foreach ($urls as $jsRef)
				{
					?>
					<script type="text/javascript" src="<?php print $jsRef; ?>"></script>
					<?php
				}
			}
		}
	}

	/**
	 * Outputs a HTML external javascript reference <script> tag.
	 * 
	 * @param string $jsRef A string url representing one javascript file to be
	 * referenced, or an array specifying a number of urls of javascript files to be
	 * referenced.
	 */
	public static function ref($jsRef)
	{
		self::src($jsRef);
	}

	/**
	 * Executes a piece of javascript code.
	 * 
	 * @param string $jsCode The javascript code to execute.
	 * <p>You are expected to include the appropriate semi-colons at the end of each expression.</p>
	 */
	public static function exec($jsCode)
	{
		if (isset($jsCode) && !empty($jsCode))
		{
			?>
			<script type="text/javascript">
			<?php print $jsCode; ?>
			</script>
			<?php
		}
	}
}
?>
