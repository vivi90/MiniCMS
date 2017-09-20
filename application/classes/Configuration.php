<?php
namespace MiniCMS;

/**
 * Configuration class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
class Configuration
{
	/**
	 * @var array Configuration values
	 */
	private $values;
	
	/**
	 * Creates a new class instance
	 * 
	 * @param string $filename Configuration file
	 */
	public function __construct($filename)
	{
		if (file_exists($filename)) {
			$this->values = include $filename;
		} else {
			die('Configuration file does not exist.');
		}
	}
	
	/**
	 * Get configuration value
	 * 
	 * @param string $key Configuration key
	 * 
	 * @return mixed Configuration value
	 */
	public function get($key)
	{
		return $this->values[$key];
	}
}
?>
