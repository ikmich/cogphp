<?php

//incomplete
class CogHeader
{

	public static function contentType($mimeType)
	{
		header("content-type: {$mimeType}");
	}

	public static function contentDisposition($disposition, $filename)
	{
		header("content-disposition: {$disposition}; filename = {$filename}");
	}

	public static function contentTransferEncoding($encoding)
	{
		header("content-transfer-encoding: {$encoding}");
	}

	public static function location($location)
	{
		header("location: {$location}");
	}

	public static function forbidden()
	{
		header('http/1.0 403 forbidden');
	}
}

?>
