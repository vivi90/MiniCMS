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
	 * @var string User request URL
	 */
	private $url;

	/**
	 * @var mixed[] User request variables
	 */
	private $variables = array();
	
	/**
	 * @var string Default language
	 */
	private $default_language;
	
	/**
     * Creates a new class instance
	 * 
	 * @param string $default_language Default language
	 */
	public function __construct($default_language)
	{
		$this->default_language = $default_language;
		// Filter URL
		$url = explode('?', $_SERVER['REQUEST_URI']);
		$url = trim($url[0], '/');
		$this->url = $this->filter($url);
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
	 * Get URL
	 * 
	 * @return string URL
	 */
	public function getURL()
	{
		return $this->url;
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
	
	/**
	 * Get user language
	 */
	public function getLanguage()
	{
		$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		if (ctype_alpha($language) && (strlen($language) == 2)) {
			return $language;
		} else {
			return $this->default_language;
		}
	}
}
?>
