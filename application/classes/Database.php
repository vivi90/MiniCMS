<?php
namespace MiniCMS;

/**
 * Database class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
class Database
{
	/**
	 * @var string Character set
	 */
	private $charset;
	
	/**
	 * @var \PDO Instance of PDO
	 */
	private $pdo;
	
	/**
	 * @var int Datatype boolean
	 */
	const TYPE_BOOL = \PDO::PARAM_BOOL;
	
	/**
	 * @var int Datatype NULL
	 */
	const TYPE_NULL = \PDO::PARAM_NULL;
	
	/**
	 * @var int Datatype integer
	 */
	const TYPE_INT = \PDO::PARAM_INT;
	
	/**
	 * @var int Datatype string
	 */
	const TYPE_STR = \PDO::PARAM_STR;
	
	/**
	 * @var int Datatype LOB
	 */
	const TYPE_LOB = \PDO::PARAM_LOB;
	
	/**
	 * Creates a new class instance
	 *
	 * @param string $host Database host
	 * @param int $port Database port
	 * @param string $database Database
	 * @param string $charset Character set
	 * @param string $user Database user
	 * @param string $password Database password
	 */
	public function __construct($host, $port, $database, $charset = "utf-8", $user, $password)
	{
		$this->charset = $charset;
		$dsn = 'mysql:host='.$host.';port='.$port.';dbname='.$database.';charset='.$this->charset;
		$this->pdo = new \PDO($dsn, $user, $password);
	}
	
	/**
	 * Prepares a statement
	 *
	 * @param string $statement SQL statement template
	 *
	 * @return Statement|bool Instance of Statement or FALSE on failure
	 */
	public function prepare($statement)
	{
		$return = $this->pdo->prepare($statement);
		if (is_object($return)) {
			if (get_class($return) == 'PDOStatement') {
				return new Statement($return);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get character set
	 * 
	 * @return string Character set
	 */
	public function getCharset()
	{
		return $this->charset;
	}
}
?>
