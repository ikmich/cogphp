<?php

class CogServer
{

	public static function authType()
	{
		return $_SERVER["AUTH_TYPE"];
	}

	public static function doc_root()
	{
		return $_SERVER["DOCUMENT_ROOT"];
	}

	public static function document_root()
	{
		return self::doc_root();
	}

	public static function gateway_interface()
	{
		return $_SERVER["GATEWAY_INTERFACE"];
	}

	public static function https_status()
	{
		return $_SERVER["HTTPS"];
	}

	public static function http_accept_header()
	{
		return $_SERVER["HTTP_ACCEPT"];
	}

	public static function http_accept_charset()
	{
		return $_SERVER["HTTP_ACCEPT_CHARSET"];
	}

	public static function http_accept_encoding()
	{
		return $_SERVER["HTTP_ACCEPT_ENCODING"];
	}

	public static function http_accept_language()
	{
		return $_SERVER["HTTP_ACCEPT_LANGUAGE"];
	}

	public static function http_host()
	{
		return $_SERVER["HTTP_HOST"];
	}

	public static function host()
	{
		return self::http_host();
	}

	public static function referer()
	{
		return $_SERVER["HTTP_REFERER"];
	}

	public static function http_user_agent()
	{
		return $_SERVER["HTTP_USER_AGENT"];
	}

	public static function path_translated()
	{
		return $_SERVER["PATH_TRANSLATED"];
	}

	public static function php_auth_digest()
	{
		return $_SERVER["PHP_AUTH_DIGEST"];
	}

	public static function php_auth_user()
	{
		return $_SERVER["PHP_AUTH_USER"];
	}

	public static function php_auth_password()
	{
		return $_SERVER["PHP_AUTH_PW"];
	}

	public static function current_script()
	{
		return $_SERVER["PHP_SELF"];
	}

	public static function php_self()
	{
		return $_SERVER["PHP_SELF"];
	}

	public static function querystring()
	{
		return $_SERVER["QUERY_STRING"];
	}

	public static function remote_addr()
	{
		return $_SERVER["REMOTE_ADDR"];
	}

	public static function remote_host()
	{
		return $_SERVER["REMOTE_HOST"];
	}

	public static function remote_port()
	{
		return $_SERVER["REMOTE_PORT"];
	}

	public static function request_method()
	{
		return $_SERVER["REQUEST_METHOD"];
	}

	public static function request_uri()
	{
		return $_SERVER["REQUEST_URI"];
	}

	public static function current_script_full_path()
	{
		return $_SERVER["SCRIPT_FILENAME"];
	}

	public static function current_script_path_from_root()
	{
		return $_SERVER["SCRIPT_NAME"];
	}

	public static function address()
	{
		return $_SERVER["SERVER_ADDR"];
	}

	public static function admin()
	{
		return $_SERVER["SERVER_ADMIN"];
	}

	public static function name()
	{
		return $_SERVER["SERVER_NAME"];
	}

	public static function port()
	{
		return $_SERVER["SERVER_PORT"];
	}

	public static function protocol()
	{
		return $_SERVER["SERVER_PROTOCOL"];
	}

	public static function signature()
	{
		return $_SERVER["SERVER_SIGNATURE"];
	}

	public static function software()
	{
		return $_SERVER["SERVER_SOFTWARE"];
	}
}

?>