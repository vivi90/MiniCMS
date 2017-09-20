<?php
namespace MiniCMS;

/**
 * Application class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
class Application
{
	/**
	 * @var Request Instance of Request
	 */
	private $request;
	
	/**
	 * @var Configuration Instance of Configuration
	 */
	private $configuration;
	
	/**
	 * @var ErrorHandler Instance of ErrorHandler
	 */
	private $errorHandler;
	
	/**
	 * @var Database Instance of Database
	 */
	protected $database;
	
	/**
	 * @var Router Instance of Router
	 */
	private $router;
	
	/**
	 * @var Page Instance of Page
	 */
	private $model;
	
	/**
	 * @var Controller Instance of Controller
	 */
	private $controller;
	
	/**
	 * @var View Instance of View
	 */
	private $view;
	
    /**
     * Creates a new class instance
	 *
	 * @param string $baseDirectory Base directory
     */
	public function __construct($baseDirectory)
	{
		$this->request = new Request();
		$this->configuration = new Configuration(
			$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config.php'
		);
		$this->errorHandler = new ErrorHandler(
			$this->configuration->get('debug'), 
			$this->configuration->get('webmaster'), 
			$this->configuration->get('webmaster_email'), 
			$this->configuration->get('project_name'), 
			$this->configuration->get('charset')
		);
		$this->database = new Database(
			$this->configuration->get('database_host'), 
			$this->configuration->get('database_port'), 
			$this->configuration->get('database_db'), 
			$this->configuration->get('database_charset'),
			$this->configuration->get('database_user'), 
			$this->configuration->get('database_password')
		);
		print_r($this->request->getPath());
		print_r($this->request->getAllVariables());				
	}
}
?>
