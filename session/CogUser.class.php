<?php

class CogUser
{

	/**
	 * The unique user id used to identify the user. Could be username, email,
	 * a numeric id, or other value.
	 * 
	 * @var string 
	 */
	protected $userRef;
	protected $loginRedirectUrl = '/';
	protected $logoutRedirectUrl = '/';
	protected static $USER_REF = 'user';

	/**
	 * The last user instance.
	 * 
	 * @var CogUser
	 */
	protected static $instance;

	protected function __construct($user = null)
	{
		$this->userRef = $user;
	}

	/**
	 * Returns the User instance.
	 * 
	 * @return CogUser 
	 */
	public static function getUser()
	{
		if (!isset(self::$instance) || self::$instance->getReference() !== CogSession::get(self::$USER_REF))
		{
			self::$instance = new CogUser(CogSession::get(self::$USER_REF));
		}
		else if (!CogSession::check(self::$USER_REF))
		{
			self::$instance = new CogUser();
		}
		return self::$instance;
	}

	public static function getInstance()
	{
		return self::getUser();
	}

	/**
	 * Returns the user reference.
	 * 
	 * @return string 
	 */
	public function getReference()
	{
		return $this->userRef;
	}

	public function getRef()
	{
		return $this->getReference();
	}

	/**
	 * Checks if the User is logged in.
	 * 
	 * @return boolean 
	 */
	public function isLoggedIn()
	{
		if (CogSession::check(self::$USER_REF))
		{
			if (CogSession::get(self::$USER_REF) == $this->userRef)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if the User is a guest (not logged in).
	 * 
	 * @return boolean
	 */
	public function isGuest()
	{
		return isset($this->userRef) && !empty($this->userRef);
	}

	/**
	 * Performs authentication on the username and password passed, using an
	 * optional authentication function if provided.
	 * 
	 * @param string $username The username
	 * @param string $password The password
	 * @param string $func [Optional] Name of function to perform authentication.
	 * @return boolean 
	 */
	public static function authenticate($values = array(), $func = null)
	{
		if (isset($values))
		{
			if (isset($func))
			{
				//Callback authentication routine.
				return call_user_func($func, $values);
			}
			else
			{
				//Custom auth routine?
//				$username = $values['username'];
//				$password = $values['password'];
//				$email = $values['email'];
			}
		}
		return false;
	}

	/**
	 * Performs a login with the user id provided. Also sets the expiry time for the session if passed.
	 * 
	 * @param mixed $userRef Variable or array or other object that holds user id info that should be kept in session.
	 * @param type $lifetime The time for the session to expire.
	 * @param type $lifetimeUnit The unit of time for the $lifetime parameter. Defaults to 'minutes'.
	 */
	public function login($userRef, $redirectUrl, $lifetime = null, $lifetimeUnit = null)
	{
		$this->userRef = $userRef;
		CogSession::save(self::$USER_REF, $this->userRef);

		if (isset($lifetime))
		{
			if (isset($lifetimeUnit))
			{
				switch ($lifetimeUnit)
				{
					case 'seconds':
					case 'sec':
					case 'secs':
					case 's':
						CogSession::setExpiry($lifetime);
						break;
					case 'min':
					case 'mins':
					case 'minute':
					case 'minutes':
						CogSession::setExpiryMinutes($lifetime);
						break;
					case 'day':
					case 'days':
						CogSession::setExpiryDays($lifetime);
						break;
					case 'week':
					case 'weeks':
					case 'wk':
					case 'wks':
						CogSession::setExpiryWeeks($lifetime);
						break;
					case 'month':
					case 'months':
					case 'mth':
					case 'mths':
						CogSession::setExpiryMonths($lifetime);
						break;
					default:
						CogSession::setExpiryMinutes($lifetime);
				}
			}
		}
		else
		{
			//Default to 5 minutes.
			CogSession::setExpiryMinutes(5);
		}
		
		if (isset($redirectUrl))
		{
			header("Location:" . $redirectUrl);
		}
		else
		{
			header("Location:" . $this->loginRedirectUrl);
		}
	}

	/**
	 * Performs a user logout.
	 */
	public function logout($redirectUrl = null)
	{
		CogSession::delete(self::$USER_REF);
		CogSession::clear();

		if (isset($redirectUrl))
		{
			header('Location:' . $redirectUrl);
		}
		else
		{
			header("Location: " . $this->logoutRedirectUrl);
		}
	}
}

?>
