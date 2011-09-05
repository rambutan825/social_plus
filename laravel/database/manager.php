<?php namespace Laravel\Database;

use Laravel\Facade;

class Manager_Facade extends Facade {

	public static $resolve = 'database';

}

class Manager {

	/**
	 * The established database connections.
	 *
	 * @var array
	 */
	public $connections = array();

	/**
	 * The connector factory instance.
	 *
	 * @var Connector\Factory
	 */
	protected $connector;

	/**
	 * The database connection configurations.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The default database connection name.
	 *
	 * @var string
	 */
	protected $default;

	/**
	 * Create a new database manager instance.
	 *
	 * @param  Connector\Factory  $connector
	 * @param  array              $config
	 * @param  string             $default
	 * @return void
	 */
	public function __construct(Connector\Factory $connector, $config, $default)
	{
		$this->config = $config;
		$this->default = $default;
		$this->connector = $connector;
	}

	/**
	 * Get a database connection. If no database name is specified, the default
	 * connection will be returned as defined in the database configuration file.
	 *
	 * Note: Database connections are managed as singletons.
	 *
	 * @param  string               $connection
	 * @return Database\Connection
	 */
	public function connection($connection = null)
	{
		if (is_null($connection)) $connection = $this->default;

		if ( ! array_key_exists($connection, $this->connections))
		{
			if ( ! isset($this->config[$connection]))
			{
				throw new \Exception("Database connection [$connection] is not defined.");
			}

			list($connector, $query, $compiler) = array($this->connector->make($this->config[$connection]), new Query\Factory, new Query\Compiler\Factory);

			$this->connections[$connection] = new Connection($connector, $query, $compiler, $connection, $this->config[$connection]);
		}

		return $this->connections[$connection];
	}

	/**
	 * Begin a fluent query against a table.
	 *
	 * This method primarily serves as a short-cut to the $connection->table() method.
	 *
	 * @param  string          $table
	 * @param  string          $connection
	 * @return Database\Query
	 */
	public function table($table, $connection = null)
	{
		return $this->connection($connection)->table($table);
	}

	/**
	 * Magic Method for calling methods on the default database connection.
	 *
	 * This provides a convenient API for querying or examining the default database connection.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}