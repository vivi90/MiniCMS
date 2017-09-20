<?php
namespace MiniCMS;

/**
 * Request class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
class Request
{
	/**
	 * @var mixed[] User request path
	 */
	private $path = array();

	/**
	 * @var mixed[] User request variables
	 */
	private $variables = array();

	/**
     * Creates a new class instance
	 */
	public function __construct()
	{
		// Filter and parse URL to get the path
		$path = explode('?', $_SERVER['REQUEST_URI']);
		$path = trim($path[0], '/');
		$path = $this->filter($path);
		$this->path = explode('/', $path);
		// Filter and flatten variables
		array_walk_recursive($_REQUEST, function(&$value, &$key) use (&$variables) { $variables[$this->filter($key)] = $this->filter($value); });
		$this->variables = $variables;
	}

	/**
	 * Filter
	 *
	 * @param string $input Input
	 *
	 * @return string Output
	 */
	private function filter($input)
	{
		return preg_replace("/[^0-9a-zA-Z_.\/-]/", "", $input);
	}

	/**
	 * Get Path
	 * 
	 * @return mixed[] Path
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Get variable
	 *
	 * @param string $id Identifier
	 * 
	 * @return mixed Value
	 */
	public function getVariable($id)
	{
		return $this->variables[$id];
	}
	
	/**
	 * Get all variables
	 * 
	 * @return mixed[] Returns an array, containing all variables
	 */
	public function getAllVariables()
	{
		return $this->variables;
	}
}
?>
