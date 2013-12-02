<?php

class CogFileOpenMode
{

	public static $READ = "r";
	public static $BIN_READ = "rb";
	public static $READ_WRITE_PREPEND = "r+";
	public static $BIN_READ_WRITE_PREPEND = "rb+";
	public static $READ_WRITE = "w+";
	public static $BIN_READ_WRITE = "wb+";
	public static $WRITE = "w";
	public static $BIN_WRITE = "wb";
	public static $WRITE_APPEND = "a";
	public static $BIN_WRITE_APPEND = "ab";
	public static $READ_WRITE_APPEND = "a+";
	public static $BIN_READ_WRITE_APPEND = "ab+";
	public static $CREATE_WRITE = "x";
	public static $BIN_CREATE_WRITE = "xb";
	public static $CREATE_READ_WRITE = "x+";
	public static $BIN_CREATE_READ_WRITE = "xb+";

	/*
	 * The use of the functions below is poor practice and are thus deprecated. 
	 * The static variables above should be used instead.
	 */

	public static function read()
	{
		return "r";
	}

	public static function bin_read()
	{
		return "rb";
	}

	public static function read_write_prepend()
	{
		return "r+";
	}

	public static function bin_read_write_prepend()
	{
		return "rb+";
	}

	public static function read_write()
	{
		return "w+";
	}

	public static function bin_read_write()
	{
		return "wb+";
	}

	public static function write()
	{
		return "w";
	}

	public static function bin_write()
	{
		return "wb";
	}

	public static function write_append()
	{
		return "a";
	}

	public static function bin_write_append()
	{
		return "ab";
	}

	public static function read_write_append()
	{
		return "a+";
	}

	public static function bin_read_write_append()
	{
		return "ab+";
	}

	public static function create_write()
	{
		return "x";
	}

	public static function bin_create_write()
	{
		return "xb";
	}

	public static function create_read_write()
	{
		return "x+";
	}

	public static function bin_create_read_write()
	{
		return "xb+";
	}
}

?>