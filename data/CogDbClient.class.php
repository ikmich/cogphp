<?php
/*
 * TODO: add apis for ALTER command
 * TOTO: complete apis for JOIN .. ON clauses
 * TODO: Use class constants.
 */

define('ACTION_CREATE_TABLE', 'create_table');
define('ACTION_SELECT', 'select');
define('ACTION_INSERT', 'insert');
define('ACTION_UPDATE', 'update');
define('ACTION_DELETE', 'delete');
define('ACTION_ALTER', 'alter');

define('INTENT_GETTING_RESULT_RESOURCE', 'getting_result_resource');
define('INTENT_GETTING_SINGLE_VALUE', 'getting_single_value');
define('INTENT_GETTING_RECORD', 'getting_record');
define('INTENT_GETTING_RECORDSET', 'getting_recordset');

/**
 * Utility class for working with mysql database and queries.
 *
 * @author ikmich
 */
class CogDbClient
{

	/**
	 * The database to work with.
	 * 
	 * @var string 
	 */
	private $database;

	/**
	 * The mysql connection user name.
	 * 
	 * @var string 
	 */
	private $username;

	/**
	 * The mysql connection password.
	 * 
	 * @var string 
	 */
	private $password;

	/**
	 * The host for database connection.
	 * 
	 * @var string 
	 */
	private $host;

	/**
	 * The port parameter for database connection.
	 * 
	 * @var string 
	 */
	private $port;

	/**
	 * The socket parameter for database connection.
	 * 
	 * @var string 
	 */
	private $socket;

	/**
	 * Boolean indicating whether a db has been selected.
	 * 
	 * @var boolean 
	 */
	private $db_selected = false;

	/**
	 * The mysqli query result returned. Could be a result resource object 
	 * or a boolean.
	 * 
	 * @var mixed
	 */
	private $resultObject;

	/**
	 * 2d associative array of query result data returned by getData($query).
	 * 
	 * @var array 
	 */
	private $resultSet = array();

	/**
	 * The variable containing result returned by getItem($query).
	 * 
	 * @var mixed 
	 */
	private $resultItem;

	/**
	 * One dimensional associative array of query result data returned by 
	 * getValue($query).
	 * 
	 * @var array 
	 */
	private $resultRecord;

	/**
	 * Indicates the name/value pairs for the active query to operate on.
	 * 
	 * @var array
	 */
	private $values = array();

	/**
	 * Indicates the columns for the active query to operate on.
	 * 
	 * @var array
	 */
	private $fields = array();

	/**
	 * List of the WHERE conditions.
	 * 
	 * @var array
	 */
	private $whereConditions = array();
	private $conditionOperators = array();
	private $limitClause;
	private $queryArgs = array(
		'where' => null,
		'order_by' => null,
		'limit' => null,
		'having' => null,
		'modify' => null,
		'distinct' => null
	);

	/**
	 * List of tables for query to operate on.
	 * 
	 * @var array
	 */
	private $tables = array();
	private $joinTables = array();
	private $joinConditions = array();

	/**
	 * List of actions.
	 * 
	 * @var array
	 */
	private $actions = array(
		ACTION_CREATE_TABLE => false,
		ACTION_INSERT => false,
		ACTION_SELECT => false,
		ACTION_UPDATE => false,
		ACTION_DELETE => false,
		ACTION_ALTER => false
	);

	/**
	 * List of intents.
	 * 
	 * @var array
	 */
	private $intents = array(
		INTENT_GETTING_RESULT_RESOURCE => true,
		INTENT_GETTING_SINGLE_VALUE => false,
		INTENT_GETTING_RECORD => false,
		INTENT_GETTING_RECORDSET => false
	);

	/**
	 * The fully constructed 'CREATE TABLE' sql statement.
	 * 
	 * @var string 
	 */
	private $query_create_table = "";

	/**
	 * Holds the table column definitions for the table creation utility methods.
	 * 
	 * @var array 
	 */
	private $tableColumns = array();

	/**
	 * Holds the table column constraints for the table creation utility methods.
	 * 
	 * @var array 
	 */
	private $tableColumnConstraints = array();

	/**
	 * Holds the meta information (e.g. ENGINE=InnoDb) for the table creation utility methods.
	 * 
	 * @var array 
	 */
	private $tableMeta = array();

	/**
	 * The mysqli connection object.
	 * 
	 * @var mysqli 
	 */
	private $conn = NULL;

	/**
	 * The static singleton instance.
	 * 
	 * @var \CogDbClient
	 */
	private static $instance;

	/**
	 * Resets values for a new operation.
	 */
	public function resetActionState()
	{
		//reset simple properties
		unset($this->resultObject);
		unset($this->resultItem);
		unset($this->queryArgs['where']);
		unset($this->queryArgs['order_by']);
		unset($this->resultSet);
		unset($this->resultRecord);
		unset($this->query_create_table);

		//reset collections
		$this->values = array();
		$this->fields = array();
		$this->tableColumnConstraints = array();
		$this->tableColumns = array();
		$this->tableMeta = array();
		$this->tables = array();
		$this->whereConditions = array();
		$this->conditionOperators = array();

		//reset intents
		foreach ($this->intents as $entry)
		{
			if ($entry == INTENT_GETTING_RESULT_RESOURCE)
			{
				$this->intents[$entry] = true;
			}
			else
			{
				$this->intents[$entry] = false;
			}
		}

		//reset actions
		foreach ($this->actions as $entry)
		{
			$this->actions[$entry] = false;
		}
	}

	/**
	 * Private constructor, for Singleton implementation. 
	 */
	private function __construct()
	{
		//Set default values.
		$this->username = '';
		$this->password = '';
		$this->host = 'localhost';
	}

	/**
	 * Used to get the current instance of CogDbClient to work with.
	 * 
	 * @return CogDbClient
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new CogDbClient();
		}
		return self::$instance;
	}

	/**
	 * Returns a new instance that is not a singleton instance.
	 * 
	 * @return \CogDbClient 
	 */
	public static function newInstance()
	{
		return new CogDbClient();
	}

	/**
	 * Sets the username for connection.
	 * 
	 * @param string $user
	 * @return CogDbClient 
	 */
	public function user($user)
	{
		$this->username = $user;
		return $this;
	}

	/**
	 * Returns the user for the session.
	 * 
	 * @return string The mysql user.
	 */
	public function getUser()
	{
		return $this->username;
	}

	/**
	 * Sets the connection password.
	 * 
	 * @param string $password The password.
	 * @return \CogDbClient
	 */
	public function password($password)
	{
		$this->password = $password;
		return $this;
	}

	/**
	 * Returns the mysql server connection password.
	 * 
	 * @return string The password.
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Sets the active database to work with.
	 * 
	 * @param string $db The database name.
	 * @return \CogDbClient	 
	 */
	public function db($db)
	{
		return $this->database($db);
	}

	/**
	 * Sets the active database.
	 * 
	 * @param string $db The database name.
	 * @return \CogDbClient
	 */
	public function database($db)
	{
		$this->database = $db;
		return $this;
	}

	/**
	 * Returns the active database.
	 * 
	 * @return string The database name.
	 */
	public function getDatabase()
	{
		return $this->database;
	}

	/**
	 * Sets the host name for the mysql server connection.
	 * 
	 * @param string $host The host name.
	 * @return CogDbClient	 
	 */
	public function host($host)
	{
		$this->host = $host;
		return $this;
	}

	/**
	 * Gets the host name for the connection.
	 * 
	 * @return string The host name.
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Sets the port for the mysql server connection.
	 * 
	 * @param string $port The port.
	 * @return CogDbClient
	 */
	public function port($port)
	{
		$this->port = $port;
		return $this;
	}

	/**
	 * Gets the port used for the mysql server connection.
	 * 
	 * @return string The port.
	 */
	public function getPort()
	{
		return $this->port;
	}

	/**
	 * Sets the socket parameter for connection.
	 * 
	 * @param string $socket
	 * @return \CogDbClient 
	 */
	public function socket($socket)
	{
		$this->socket = $socket;
		return $this;
	}

	/**
	 * Returns the socket parameter for connection.
	 * 
	 * @return string 
	 */
	public function getSocket()
	{
		return $this->socket;
	}

	/**
	 * Connects to the mysql server using the username, password, host, port, socket
	 * and database provided if any. Returns true or false depending on success. If 
	 * successful, and an active database has been set, it selects the database for use.
	 * 
	 * @return boolean
	 */
	public function connect($params = array())
	{
		if (!empty($params))
		{
			if (isset($params['host']))
			{
				$this->host = $params['host'];
			}

			if (isset($params['user']))
			{
				$this->username = $params['user'];
			}
			else if (isset($params['username']))
			{
				$this->username = $params['username'];
			}

			if (isset($params['password']))
			{
				$this->password = $params['password'];
			}
			else if (isset($params['pw']))
			{
				$this->password = $params['pw'];
			}
			else if (isset($params['pwd']))
			{
				$this->password = $params['pwd'];
			}

			if (isset($params['db']))
			{
				$this->database = $params['db'];
			}
			else if (isset($params['database']))
			{
				$this->database = $params['database'];
			}

			if (isset($params['port']))
			{
				$this->port = $params['port'];
			}

			if (isset($params['socket']))
			{
				$this->socket = $params['socket'];
			}
		}

		$this->conn = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port, $this->socket);
		if (!$this->conn)
		{
			return false;
		}

		//else, success.. select the database if it was provided..
		if (is_string($this->database) && !empty($this->database))
		{
			$this->selectDb($this->database);
		}

		return $this->conn;
	}

	/**
	 * Gets the active mysqli connection object for this instance.
	 * 
	 * @return resource The mysqli connection object returned by call to new mysqli(params...)
	 */
	public function getConnection()
	{
		return $this->conn;
	}

	public function isConnected()
	{
		return isset($this->conn);
	}

	/**
	 * Selects a database for use.
	 * 
	 * @param string $db The database to select.
	 * @return boolean Returns true on success. 
	 */
	public function selectDb($db)
	{
		$flag = $this->conn->select_db($db);
		if ($flag)
		{
			$this->db_selected = true;
		}
		return $flag;
	}

	public function dbExists($dbName)
	{
		try
		{
			if (!$this->conn)
			{
				$this->errorNotice("Not connected to database server.", true);
			}

			$flag = $this->selectDb($dbName);
			return $flag;
		}
		catch (Exception $ex)
		{
			$ex = null;
			return false;
		}
	}

	/**
	 * Creates a database if it does not exist.
	 * 
	 * @param string $db The database to create.
	 * @return boolean True on success; False otherwise.
	 */
	public function createDb($db)
	{
		if (!$this->conn)
		{
			$this->errorNotice("Not connected to database server.", true);
		}

		$flag = $this->conn->query("CREATE DATABASE IF NOT EXISTS {$db}");
		if ($flag)
		{
			//db created.. select it..
			$this->selectDb($db);
		}
		return $flag;
	}

	/**
	 * Creates a new database regardless of whether it exists or not.
	 * 
	 * @param string $db The database name.
	 * @return boolean True if successful; False otherwise.
	 */
	public function recreateDb($db)
	{
		if (!$this->conn)
		{
			$this->errorNotice("Not connected to database server.", true);
		}

		if ($this->conn->query("DROP DATABASE {$db}"))
		{
			$bool = $this->conn->query("CREATE DATABASE {$db}");
			if ($bool)
			{
				//Db created. Select it.
				$this->selectDb($db);
			}
			return $bool;
		}

		//else... database does not exist? create it..
		$bool = $this->createDb($db);
		return $bool;
	}

	/**
	 * Starts a DROP DATABASE command.
	 * 
	 * @param string $db The database to drop.
	 * @return boolean 
	 */
	public function dropDb($db)
	{
		$bool = $this->conn->query("DROP DATABASE {$db}");
		return $bool;
	}

	/**
	 * Starts a CREATE TABLE command.
	 * 
	 * @param string $table The table to create.
	 * @return \CogDbClient 
	 */
	public function createTable($table)
	{
		$this->newAction(ACTION_CREATE_TABLE);
		$this->query_create_table .= "CREATE TABLE {$table} (";
		return $this;
	}

	/**
	 * Starts a CREATE TEMPORARY TABLE command.
	 * 
	 * @param string $table The table to create.
	 * @return \CogDbClient 
	 */
	public function createTempTable($table)
	{
		$this->newAction(ACTION_CREATE_TABLE);
		$this->query_create_table .= "CREATE TEMPORARY TABLE {$table} IF NOT EXISTS (";
		return $this;
	}

	/**
	 * Adds a table column for a CREATE TABLE command.
	 * 
	 * @param string $tableColumn <p>The table column name.</p>
	 * @return \CogDbClient 
	 */
	public function tableColumn($tableColumn)
	{
		$this->tableColumns[] = $tableColumn;
		return $this;
	}

	/**
	 * Adds a series of columns for a CREATE TABLE command.
	 * 
	 * @param array $tableColumns
	 * @return \CogDbClient
	 */
	public function tableColumns($tableColumns = array())
	{
		$this->tableColumns = $tableColumns;
		return $this;
	}

	/**
	 * Adds table constraints for a CREATE TABLE operation.
	 * 
	 * @param string $constraint <p>The constraint.</p>
	 * @return \CogDbClient 
	 */
	public function tableConstraint($constraint)
	{
		$this->tableColumnConstraints[] = $constraint;
		return $this;
	}

	/**
	 * Sets table constraints for a CREATE TABLE operation.
	 * 
	 * @param array $constraints <p>Array containing the constraints.</p>
	 * @return \CogDbClient
	 */
	public function tableConstraints($constraints = array())
	{
		$this->tableConstraints = $constraints;
		return $this;
	}

	/**
	 * Adds meta information for a CREATE TABLE operation.
	 * 
	 * @param string $meta <p>Meta info for the table creation sql query.</p>
	 * @return \CogDbClient 
	 */
	public function tableMeta($meta)
	{
		$this->tableMeta[] = $meta;
		return $this;
	}

	/**
	 * Sets meta information for a CREATE TABLE operation.
	 * 
	 * @param array $metas <p>Array containing each meta information.</p>
	 * @return \CogDbClient
	 */
	public function tableMetas($metas = array())
	{
		$this->tableMeta = $metas;
		return $this;
	}

	/**
	 * Starts a DROP TABLE command.
	 * 
	 * @param string $table <p>The table to drop.</p>
	 * @return bool 
	 */
	public function dropTable($table)
	{
		$bool = $this->conn->query("DROP TABLE {$table}");
		return $bool;
	}

	/**
	 * Executes some house keeping processes required when a new action is 
	 * to be embarked on.
	 * 
	 * @param string $action
	 */
	private function newAction($action)
	{
		//foreach ($this->actions as $key => $value)
		foreach (array_keys($this->actions) as $key)
		{
			$this->actions[$key] = false;
		}

		$this->actions[$action] = true;

		unset($this->values);
		unset($this->fields);
		unset($this->tables);
		unset($this->queryArgs['where']);
		unset($this->queryArgs['order_by']);
	}

	/**
	 * Indicate the "intent" for the current operation.
	 * 
	 * @param string $intent
	 */
	private function setIntent($intent)
	{
		if ($intent == INTENT_GETTING_RESULT_RESOURCE)
		{
			foreach ($this->intents as $key => $value)
			{
				if ($intent != INTENT_GETTING_RESULT_RESOURCE)
				{
					$this->intents[$key] = false;
				}
				else
				{
					$this->intents[$key] = true;
				}
			}
		}
		else
		{
			foreach ($this->intents as $key => $value)
			{
				if ($key == $intent)
				{
					//set this to true....
					$this->intents[$key] = true;
				}
				else
				{
					//and the rest to false...
					$this->intents[$key] = false;
				}
			}
		}
	}

	/**
	 * Starts a SELECT * operation, to select all records in a query. The
	 * eventual return value upon calling run() would be a recordset (2-D 
	 * associative array);
	 * 
	 * @return \CogDbClient 
	 */
	public function selectAll()
	{
		$this->newAction(ACTION_SELECT);
		unset($this->fields);
		$this->setIntent(INTENT_GETTING_RESULT_RESOURCE);
		return $this;
	}

	/**
	 * Starts a SELECT * operation, to select all records in a query. The 
	 * eventual return value upon calling run() would be a recordset (2-D
	 * associative array).
	 * 
	 * @return \CogDbClient
	 */
	public function getAll()
	{
		$this->selectAll();
		$this->setIntent(INTENT_GETTING_RECORDSET);
		return $this;
	}

	/**
	 * Starts a SELECT operation. The eventual return value upon calling run()
	 * would be a mysqli result resource.
	 * 
	 * @param string $fields <p>Comma-separated list of column names.</p>
	 * @return \CogDbClient 
	 */
	public function select($fields)
	{
		$this->newAction('select');
		$this->fields = preg_split('<,\s*>i', $fields);
		$this->setIntent(INTENT_GETTING_RESULT_RESOURCE);
		return $this;
	}

	/**
	 * Starts a SELECT operation. The eventual return value upon calling run()
	 * would be a recordset (2-D Associative Array).
	 * 
	 * @param string $fields <p>Comma-separated list of column names.</p>
	 * @return \CogDbClient
	 */
	public function getValue($fields)
	{
		$this->newAction(ACTION_SELECT);
		$this->fields = preg_split('<,\s*>i', $fields);
		$this->setIntent(INTENT_GETTING_RECORDSET);
		return $this;
	}

	/**
	 * Indicates the DISTINCT attribute for a query.
	 * 
	 * @return \CogDbClient
	 */
	public function distinct()
	{
		$this->queryArgs['distinct'] = true;
		return $this;
	}

	private function arrayToStringList($array)
	{
		$i = 0;
		$str = '';
		foreach ($array as $entry)
		{
			$str += $entry;
			if ($i < count($array) - 1)
			{
				$str += ', ';
			}
			$i++;
		}
		return $str;
	}

	/**
	 * Indicates the table that is the subject of the current operation, most
	 * likely a SELECT.
	 * 
	 * @param string $tables Comma-separated list of tables.
	 * @return \CogDbClient 
	 */
	public function from($tables)
	{
		unset($this->tables);
		$this->tables = preg_split('<,\s*>i', $tables);
		return $this;
	}

	public function table($tables)
	{
		return $this->from($tables);
	}

	/**
	 * Starts an UPDATE TABLE command.
	 * 
	 * @param string $tables Comma-separated list of tables.
	 * @return \CogDbClient 
	 */
	public function update($tables)
	{
		$this->newAction(ACTION_UPDATE);
//		unset($this->tables);
		$this->tables = preg_split('<\,\s*>i', $tables);
		return $this;
	}

	/**
	 * Starts a DELETE command.
	 * 
	 * @return \CogDbClient 
	 */
	public function delete()
	{
		$this->newAction('delete');
		return $this;
	}

	/**
	 * Adds a name/value pair to the 'values' array property of this instance.
	 * 
	 * @param string $name The name of the key/column.
	 * @param mixed $value The value.
	 * @return \CogDbClient
	 */
	public function set($name, $value)
	{
		if (isset($name) && !empty($name))
		{
			$this->values[$name] = $value;
		}
		return $this;
	}

	/**
	 * Sets the values for an UPDATE or INSERT operation.
	 * 
	 * @param array $values Associative array of name-value pairs.
	 */
	public function values($values = array())
	{
		$this->values = $values;
		return $this;
	}

	/**
	 * Starts an INSERT operation.
	 * 
	 * @param string $table The table to insert values into.
	 * @return \CogDbClient 
	 */
	public function insertInto($table)
	{
		$this->newAction(ACTION_INSERT);
		$this->tables[] = $table;
		return $this;
	}

	/**
	 * Parses a field value to determine whether a string value should
	 * be automatically quoted for db operations.
	 * 
	 * @param string $value
	 * @return string The parsed value.
	 */
	public static function quotifyForQuery($value)
	{
		if (!isset($value))
		{
			return '';
		}

		$pattern_quoted = '<(^\".*\"$)|(^\'.*\'$)>';
		if (is_string($value))
		{
			if (!preg_match($pattern_quoted, $value))
			{
				//not quoted string. quote it.
				$value = "'" . $value . "'";
			}
			else
			{
				//quoted. replace double quote with single quote...
				$value = preg_replace('<^\"|\"$>', '', $value);
			}
		}

		return $value;
	}

	/**
	 * Implements a WHERE clause in the current operation.
	 * 
	 * @param string $conditionString The conditions for the WHERE clause.
	 * @return \CogDbClient 
	 */
	public function where($conditionString)
	{
		unset($this->whereConditions);
		$this->queryArgs['where'] = $conditionString;
		return $this;
	}

	/**
	 * Implements a WHERE clause with a specific "equals to" condition.
	 * 
	 * @param string $field The name of the column.
	 * @param mixed $value The value of the entry.
	 * @return \CogDbClient
	 */
	public function whereEquals($field, $value)
	{
		unset($this->queryArgs['where']);
		if (isset($value))
		{
			$this->whereConditions[] = "{$field} = " . self::quotifyForQuery($value);
		}
		else
		{
			$this->whereConditions[] = "{$field} is null";
		}

		return $this;
	}

	public function whereNotEquals($field, $value)
	{
		unset($this->queryArgs['where']);
		if (isset($value))
		{
			$this->whereConditions[] = "{$field} != " . self::quotifyForQuery($value);
		}
		else
		{
			$this->whereConditions[] = "{$field} is not null";
		}

		return $this;
	}

	/**
	 * Implements a WHERE clause with a specific "greater than" condition.
	 * 
	 * @param string $field The name of the column.
	 * @param mixed $value The value of the entry.
	 * @return \CogDbClient
	 */
	public function whereGreaterThan($field, $value)
	{
		unset($this->queryArgs['where']);
		$this->whereConditions[] = "{$field} > " . self::quotifyForQuery($value);
		return $this;
	}

	/**
	 * Implements a WHERE clause with a specific "less than" condition.
	 * 
	 * @param string $field The name of the column.
	 * @param mixed $value The value of the entry.
	 * @return \CogDbClient
	 */
	public function whereLessThan($field, $value)
	{
		unset($this->queryArgs['where']);
		$this->whereConditions[] = "{$field} < " . self::quotifyForQuery($value);
		return $this;
	}

	/**
	 * Implements a	WHERE clause with a specific "greater than or equals"
	 * condition.
	 * 
	 * @param string $field The name of the column.
	 * @param mixed $value The value of the entry.
	 * @return \CogDbClient
	 */
	public function whereGreaterThanOrEquals($field, $value)
	{
		unset($this->queryArgs['where']);
		$this->whereConditions[] = "{$field} >= " . self::quotifyForQuery($value);
		return $this;
	}

	/**
	 * Implements a	WHERE clause with a specific "less than or equals" condition.
	 * 
	 * @param string $field The name of the column.
	 * @param mixed $value The value of the entry.
	 * @return \CogDbClient
	 */
	public function whereLessThanOrEquals($field, $value)
	{
		unset($this->queryArgs['where']);
		$this->whereConditions[] = "{$field} <= " . self::quotifyForQuery($value);
		return $this;
	}

	/**
	 * Implements a	WHERE LIKE comparison condition.
	 * 
	 * @param string $field The name of the column.
	 * @param string $comparison String representing the value to compare to.
	 * @return \CogDbClient
	 */
	public function whereLike($field, $comparison)
	{
		unset($this->queryArgs['where']);
		$this->whereConditions[] = "{$field} LIKE " . self::quotifyForQuery($comparison);
		return $this;
	}

	/**
	 * Implements a WHERE IN condition.
	 * 
	 * @param string $field The name of the column.
	 * @param array $values Array containing values to check.
	 * @return \CogDbClient
	 */
	public function whereIn($field, $values)
	{
		unset($this->queryArgs['where']);
		if (is_array($values) && !empty($values))
		{
			//$values_string = '';
			foreach ($values as $value)
			{
				$values_string .= self::quotifyForQuery($value) . ", ";
			}
			//remove trailing comma and space
			$values_string = preg_replace('<\,*\s*$>', '', $values_string);
			$this->whereConditions[] = "{$field} IN ({$values_string})";
		}
		return $this;
	}
//	public function join($tables)
//	{
//		$this->joinTables = preg_split('<\,\s*>i', $tables);
//		return $this;
//	}
//	public function on($joinConditions)
//	{
//		$this->joinConditions[] = $joinConditions;
//		return $this;
//	}

	/**
	 * Adds an AND operator to the query.
	 * 
	 * @return \CogDbClient
	 */
	public function _and()
	{
		$this->conditionOperators[] = ' AND ';
		return $this;
	}

	/**
	 * Adds an OR operator to the query.
	 * 
	 * @return \CogDbClient
	 */
	public function _or()
	{
		$this->conditionOperators[] = ' OR ';
		return $this;
	}

	/**
	 * Implements an ORDER BY clause in the current operation.
	 * 
	 * @param string $orderByClause
	 * @return \CogDbClient 
	 */
	public function orderBy($orderByClause)
	{
		$this->queryArgs['order_by'] = $orderByClause;
		return $this;
	}

	/**
	 * Implements a LIMIT clause in the current operation.
	 * 
	 * @param string $limitClause
	 * @return \CogDbClient
	 */
	public function limit($limitClause)
	{
		$this->limitClause = $limitClause;
		return $this;
	}

	/**
	 * Checks whether the supplied query returns a result resource or whether it
	 * returns data.
	 * 
	 * @param string $query
	 * @return boolean True if the query returns data. False otherwise. 
	 */
	public static function queryReturnsResult($query)
	{
		/*
		 * if it's a SELECT, SHOW, DESCRIBE, or EXPLAIN query, it returns a result object
		 * to be used to extract the actual result data..
		 */
		return preg_match('<^select\s>i', $query) || preg_match('<^show\s>i', $query) || preg_match('<^describe>s/i', $query) || preg_match('<^explain>s/i', $query);
	}

	private function buildQueryValuesString()
	{
		$queryValues = "";
		if (isset($this->values) && count($this->values) > 0)
		{
			$queryValues .= " SET";
			foreach ($this->values as $key => $value)
			{
				if (is_string($value))
				{
					$value = self::quotifyForQuery($value);
				}
				$queryValues .= " {$key} = {$value}, ";
			}
			//remove trailing comma and space..
			$queryValues = preg_replace('<(,\s*)$>', '', $queryValues);
		}
		return $queryValues;
	}

	private function buildQueryColumnsString()
	{
		$queryColumns = "";
		if (isset($this->fields) && count($this->fields) > 0)
		{
			foreach ($this->fields as $field)
			{
				$queryColumns .= " {$field}, ";
			}
			//remove trailing comma and space..
			$queryColumns = preg_replace('<(,\s*)$>', '', $queryColumns);
		}
		return $queryColumns;
	}

	private function buildQueryTablesString()
	{
		$queryTables = "";
		if (isset($this->tables) && count($this->tables) > 0)
		{
			foreach ($this->tables as $table)
			{
				$queryTables .= "{$table}, ";
			}

			//remove trailing comma and space..
			$queryTables = preg_replace('<(,\s*)$>', '', $queryTables);
		}
		return $queryTables;
	}

	private function buildWhereString()
	{
		$whereString = "";

		if (isset($this->whereConditions) && !empty($this->whereConditions))
		{
			for ($i = 0; $i < count($this->whereConditions); $i++)
			{
				$whereString .= " {$this->whereConditions[$i]}";
				if (isset($this->conditionOperators[$i]))
				{
					$whereString .= " {$this->conditionOperators[$i]}";
				}
				else
				{
					//No more 'AND's or 'OR's.
					break;
				}
			}
		}
		else if (isset($this->queryArgs['where']))
		{
			$whereString = $this->queryArgs['where'];
		}

		return $whereString;
	}

	private function buildOrderByString()
	{
		//isset($this->queryArgs['order_by']) ? " ORDER BY {$this->queryArgs['order_by']}" : "";
		if (isset($this->queryArgs['order_by']))
		{
			return $this->queryArgs['order_by'];
		}
		return '';
	}

	/**
	 * Runs a query if supplied or run the currently constructed operation.
	 * 
	 * @param string $query [Optional] MySQL query string to run. 
	 * If not provided, it will attempt to run the built-in query.
	 * 
	 * @return mixed <p>Depending on the action methods called on the instance before
	 * calling run(), the return value could return the result object for 
	 * the executed query; or a recordset (2-d associative array); or a
	 * single value.</p>
	 *  
	 */
	public function run($query = null)
	{
		$this->checkDbSelected();

		if (!is_null($query))
		{
			//run the supplied mysql query string...
			$query = @func_get_arg(0);
			$this->resultObject = $this->conn->query($query);
			return $this->resultObject;
		}

		/*
		 * else... run the built-in query...
		 */

		//if creating a table..
		if ($this->actions[ACTION_CREATE_TABLE] === true)
		{
			foreach ($this->tableColumns as $tableColumn)
			{
				$this->query_create_table .= "{$tableColumn}, ";
			}

			foreach ($this->tableColumnConstraints as $constraint)
			{
				$this->query_create_table .= "{$constraint}, ";
			}

			//remove trailing comma and space from the create table string
			$this->query_create_table = preg_replace('/(,*\s*)$/', '', $this->query_create_table);

			//add the closing bracket..
			$this->query_create_table .= ") ";

			//add table meta information if any.
			foreach ($this->tableMeta as $meta)
			{
				$this->query_create_table .= "{$meta} ";
			}

			//remove trailing spaces..
			$this->query_create_table = rtrim($this->query_create_table);

			//Cog::printlnxgreen('create table query: ' . $this->query_create_table);

			$this->resultObject = $this->conn->query($this->query_create_table);
			return $this->onAfterRun($this->resultObject);
		}

		$queryColumns = $this->buildQueryColumnsString();
		$queryTables = $this->buildQueryTablesString();
		$queryValues = $this->buildQueryValuesString();
		$whereString = $this->buildWhereString();
		$query = "";

		/*
		 * SELECT
		 */
		if ($this->actions[ACTION_SELECT] === true)
		{
			$query .= "SELECT";

			if (isset($this->queryArgs['distinct']))
			{
				$query .= " DISTINCT";
			}

			if (!empty($queryColumns))
			{
				$query .= " {$queryColumns}";
			}
			else
			{
				$query .= " *";
			}

			$query .= " FROM";
			if (!empty($queryTables))
			{
				$query .= " {$queryTables}";
			}

			if (isset($whereString) && !empty($whereString))
			{
				$query .= " WHERE {$whereString}";
			}

			$orderByString = $this->buildOrderByString();
			if (isset($orderByString) && !empty($orderByString))
			{
				$query .= ' ORDER BY ' . $orderByString;
			}

			//remove any trailing spaces..
			$query = rtrim($query);

			//test output:
			//Cog::printlnblue('select query: ' . $query);
			//run the query..
			$this->resultObject = $this->conn->query($query);

			if ($this->getError())
			{
				$this->errorNotice($this->getError());
			}

			if ($this->intents[INTENT_GETTING_RESULT_RESOURCE])
			{
				return $this->onAfterRun($this->resultObject);
			}

			if ($this->intents[INTENT_GETTING_SINGLE_VALUE])
			{
				return $this->onAfterRun($this->getValueFromQuery($query));
			}

			if ($this->intents[INTENT_GETTING_RECORDSET])
			{
				return $this->onAfterRun($this->getRecordSet($query));
			}

			return $this->onAfterRun($this->resultObject);
		}

		/*
		 * INSERT
		 */
		else if ($this->actions[ACTION_INSERT] === true)
		{
			$query = "INSERT INTO";
			if (isset($this->tables) && !empty($this->tables[0]))
			{
				$query .= " {$this->tables[0]}";
			}

			if (!empty($queryValues))
			{
				$query .= $queryValues;
			}

			//run the query..
//			Cog::printlnxblue("insert query: <br />" . $query);

			$this->resultObject = $this->conn->query($query);

			if ($this->getError())
			{
				$this->errorNotice($this->getError());
			}

			return $this->onAfterRun($this->resultObject);
		}

		/*
		 * UPDATE
		 */
		else if ($this->actions[ACTION_UPDATE] === true)
		{
			$query = "UPDATE";

			if (isset($this->tables) && !empty($this->tables[0]))
			{
				$query .= " {$this->tables[0]}";
			}

			if (!empty($queryValues))
			{
				$query .= $queryValues;
			}

			//the where clause
			//$query .= isset($this->queryArgs['where']) ? " WHERE {$this->queryArgs['where']}" : "";
			if (isset($whereString))
			{
				$query .= " WHERE {$whereString}";
			}

			//run the query..
//			Cog::printlnxBlue('update query: <br />' . $query);

			$this->resultObject = $this->conn->query($query);

			if ($this->getError())
			{
				$this->errorNotice($this->getError());
			}

			return $this->onAfterRun($this->resultObject);
		}

		/*
		 * DELETE
		 */
		else if ($this->actions[ACTION_DELETE] === true)
		{
			$query = "DELETE FROM";
			if (isset($this->tables) && !empty($this->tables[0]))
			{
				$query .= " {$this->tables[0]}";
			}

			if (!empty($whereString))
			{
				$query .= " WHERE {$whereString}";
			}

			//run the query..
//			Cog::printlnxblue('delete query: ' . $query);

			$this->resultObject = $this->conn->query($query);

			if ($this->getError())
			{
				$this->errorNotice($this->getError());
			}

			return $this->onAfterRun($this->resultObject);
		}

//		Cog::printLnBlue('tables: ' . $queryTables);
//		Cog::printlnBlue('columns: ' . $queryColumns);
//		Cog::printLnBlue('values: ' . $queryValues);
//		Cog::printlnBlue('query: ' . $query);
//		Cog::println('');
	}

	private function onAfterRun($value)
	{
		$this->resetActionState();
		return $value;
	}

	/**
	 * Returns the result object for the last query run.
	 * 
	 * @return resource 
	 */
	public function getResultObject()
	{
		return $this->resultObject;
	}

	/**
	 * Gets data result from a query.
	 * 
	 * @param string $query
	 * @return array|false|null 2-d associative array representing the data retrieved, or 
	 * false if not successful or null if the query does not return data.
	 */
	public function getData($query)
	{
		$this->checkDbSelected();
		$this->resultObject = $this->conn->query($query);
		if ($this->resultObject)
		{
			if (self::queryReturnsResult($query))
			{
				while ($row = mysqli_fetch_assoc($this->resultObject))
				{
					$this->resultSet[] = $row;
				}

				if (isset($this->resultSet))
				{
					return $this->resultSet;
				}
			}
			return null;
		}
		return false;
	}

	/**
	 * Alias for getData($query)
	 * 
	 * @param string $query
	 * @return array|false|null 
	 */
	public function getRecordSet($query)
	{
		return $this->getData($query);
	}

	/**
	 * Internal function to check if the database is selected and prevents 
	 * further execution if not.
	 */
	private function checkDbSelected()
	{
		if (!$this->db_selected)
		{
			$this->errorNotice('No database selected.');
		}
	}

	/**
	 * Returns a single row of data returned by a MySQL query. Always returns one
	 * row data even if the query returns data spanning more than one row. 
	 * The first row is taken in such a case.
	 * 
	 * @param string $query
	 * @return array|null Associative array or null if unsuccessful or if the
	 * query does not return data.
	 */
	public function getRecord($query)
	{
		$this->checkDbSelected();
		$this->resultObject = $this->conn->query($query);
		if ($this->resultObject && self::queryReturnsResult($query))
		{
			while ($row = mysqli_fetch_assoc($this->resultObject))
			{
				$this->resultRecord = $row;
				break;
			}
			return $this->resultRecord;
		}
		return null;
	}

	/**
	 * Returns a single value returned by a MySQL query.
	 * 
	 * @param string $query
	 * @return mixed|null The data value returned or null if unsuccessful or
	 * no data is returned or the query does not return data.
	 */
	public function getValueFromQuery($query)
	{
		$this->checkDbSelected();
		$this->resultObject = $this->conn->query($query);
		if ($this->resultObject && self::queryReturnsResult($query))
		{
			while ($row = mysqli_fetch_row($this->resultObject))
			{
				$this->resultItem = $row[0];
				break;
			}
			//return isset($this->resultItem) ? $this->resultItem : null;
			return $this->resultItem;
		}
		return null;
	}

	/**
	 * Executes an sql file using a shell command.
	 * 
	 * @param string $filepath The path to the sql file to run.
	 * @return bool The output of the command.
	 */
	public function runSqlFile($filepath)
	{
		//Ensure extension is .sql
		if (CogFile::checkExtension($filepath, '.sql'))
		{
			$command = "mysql -u {$this->username} --password={$this->password} {$this->database} < {$filepath}";
			//$cmdOutput = shell_exec($command);
			//return $cmdOutput;
			$status = 0;
			$output = array();
			exec($command, $output, $status);
			if ($status == 0)
			{
				return true;
			}
			return false;
		}

		return false;
	}
//	private function dumpSql($settings = array())
//	{
//		if (empty($settings)) {
//			//dump whole database
//		}
//		else {
//			//dump data according to settings
//			if (isset($settings['tables']) && count($settings['tables']) > 0) {
//				
//			}
//			if (isset($settings['separate_tables'])) {
//				
//			}
//		}
//	}

	/**
	 * Returns the last error code.
	 * 
	 * @return int 
	 */
	public function getErrorCode()
	{
		return $this->conn->errno;
	}

	/**
	 * Returns the last error.
	 * 
	 * @return string 
	 */
	public function getError()
	{
		return $this->conn->error;
	}

	/**
	 * Returns the error code on connection attempt if any.
	 * 
	 * @return string 
	 */
	public function getConnectionErrorCode()
	{
		return $this->conn->connect_errno;
	}

	/**
	 * Returns the error on connection attempt if any.
	 * 
	 * @return string 
	 */
	public function getConnectionError()
	{
		return $this->conn->connect_error;
	}

	/**
	 * Internally used to print out an error.
	 * 
	 * @param string $msg
	 */
	private function errorNotice($msg)
	{
		$fullMsg = "Error! {$msg}";
//		$cogError = new CogError();
//		$fullMsg .= "<br /><br /> {$cogError->getMessage()}";
//		$fullMsg .= "<br /> {$cogError->getFile()}";
//		$fullMsg .= "<br /> {$cogError->getLineNumber()}";
		die("<div style='clear:both;'></div>
			<div style='color: #ffffff; font-weight: bold; font-family: sans-serif; font-size: 11px; text-shadow: 0px 1px 0px #040404; cursor: default; float: left; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; padding: 7px 8px 6px 8px; color: #ffffff; background-color: #cc0000; '>{$fullMsg}</div>
				<div style='clear:both;'></div>"
		);
	}

	/**
	 * Tests the error state of the CogDbClient instance.
	 * 
	 * @return boolean
	 */
	public function ok()
	{
		if ($this->getError() || $this->getConnectionError())
		{
			return false;
		}
		return true;
	}

	/**
	 * Starts a transaction.
	 */
	public function startTransaction()
	{
		$this->conn->autocommit(false);
	}

	/**
	 * Ends a transaction.
	 */
	public function stopTransaction()
	{
		$this->conn->rollback();
		$this->conn->autocommit(true);
	}

	/**
	 * Ends a transaction.
	 */
	public function endTransaction()
	{
		$this->stopTransaction();
	}

	/**
	 * Commits the current transaction.
	 */
	public function commitTransaction()
	{
		$this->conn->commit();
		$this->conn->autocommit(true);
	}

	/**
	 * Closes the db connection.. 
	 */
	public function close()
	{
		$this->conn->close();
		$this->conn = null;
	}
}

?>
