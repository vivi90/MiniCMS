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
	private $database;
	
	/**
	 * @var Router Instance of Router
	 */
	private $router;
	
	/**
	 * @var Page Instance of Page
	 */
	private $model;
	
	/**
	 * @var View Instance of View
	 */
	private $view;
	
	/**
	 * @var Controller Instance of Controller
	 */
	private $controller;
	
	/**
	 * Creates a new class instance
	 *
	 * @param string $baseDirectory Base directory
	 */
	public function __construct($baseDirectory)
	{
		// Initialization
		$this->configuration = new Configuration(
			$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'config.php'
		);
		$this->request = new Request(
			$this->configuration->get('default_language')
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
		// Routing
		$route = $this->getRoute(
			$this->database,
			$this->request->getURL(),
			$this->request->getLanguage(),
			$baseDirectory
		);
		// Run
		$this->model = new $route['model'](
			$this->database
		);
		$this->view = new $route['view'](
			$this->model
		);
		$this->controller = new $route['controller'](
			$this->model,
			$this->request
		);
		$this->controller->$route['action'](
			$route['parameters']['page_id'],
			$route['parameters']['language']
		);
		$this->view->response();
	}
	
	/**
	 * Get route
	 * 
	 * @param Database $database Database connection
	 * @param string $url URL
	 * @param string $default_language Default language
	 * @param string $baseDirectory Base directory
	 * 
	 * @return array model, view, controller, action and parameters
	 */
	private function getRoute(Database $database, $url, $default_language, $baseDirectory)
	{
		if (empty($url)) {
			// Redirect to the default page
			$statement = $this->database->prepare(
				"SELECT url FROM routes WHERE language = :language AND page_id = (
					SELECT id FROM pages WHERE is_default_page = 1 LIMIT 1
				)"
			);
			$statement->bindValue(":language", $default_language, Database::TYPE_STR);
			$statement->execute();
			$result = $statement->fetch();
			if (empty($result)) {
				// '404 Not Found' page not found
				trigger_error("Default page not found.", E_USER_ERROR);
			} else {
				// Return route to the '404 Not Found' page
				header('Location: '.$result['url']);
			}
		} else {
			// Get the route from the database
			$statement = $this->database->prepare("SELECT page_id, language FROM routes WHERE url = :url");
			$statement->bindValue(":url", $url, Database::TYPE_STR);
			$statement->execute();
			$result = $statement->fetch();
			if (empty($result)) {
				// Create route, if no one in the database exists
				$route =  explode('/', $url);
				$route = array(
					'model' => $route[0],
					'view' => $route[1],
					'controller' => $route[2],
					'action' => $route[3],
					'parameters' => array(
						'page_id' => $route[4],
						'language' => $route[5]
					)
				);
			} else {
				// Route from the database
				$route = array(
					'model' => 'PageModel',
					'view' => 'PageView',
					'controller' => 'PageController',
					'action' => 'load',
					'parameters' => array(
						'page_id' => $result['page_id'],
						'language' => $result['language']
					)
				);
			}
			// Check the route
			if (
				file_exists($baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.$route[0]) && 
				file_exists($baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.$route[1]) && 
				file_exists($baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.$route[2])
			) {
				// Return route
				return $route;
			} else {
				// '404 Not Found' page
				$statement = $this->database->prepare("SELECT id FROM pages WHERE is_not_found_page = 1 LIMIT 1");
				$statement->execute();
				$result = $statement->fetch();
				if (empty($result)) {
					// '404 Not Found' page not found
					trigger_error("'404 Not Found' page not found.", E_USER_ERROR);
				} else {
					// Return route to the '404 Not Found' page
					return array(
						'model' => 'PageModel',
						'view' => 'NotFoundPageView',
						'controller' => 'PageController',
						'action' => 'load',
						'parameters' => array(
							'page_id' => $result['id'],
							'language' => $default_language
						)
					);
				}
			}
		}
	}
}
?>
