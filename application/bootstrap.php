<?php
namespace MiniCMS;

/**
 * Bootstrap
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
 
/**
 * Check PHP version
 */
$required_php_version = '5.5.0';
if (version_compare(PHP_VERSION, $required_php_version) < 0) {
    die('Require PHP '.$required_php_version.' or above.');
}


/**
 * Set class autoloading
 */
set_include_path(
	get_include_path().
	PATH_SEPARATOR.$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.
	PATH_SEPARATOR.$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'models'.
	PATH_SEPARATOR.$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'views'.
	PATH_SEPARATOR.$baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'controllers'
);
spl_autoload_register(
	function ($fqn) {
		$fqn = explode('\\', $fqn);
		$class = end($fqn);
		$paths = explode(PATH_SEPARATOR, get_include_path());
		foreach ($paths as $path) {
			$success = TRUE;
			$filepath = $path.DIRECTORY_SEPARATOR.$class.".php";
			if (file_exists($filepath)) {
				require_once $filepath;
				break;
			} else {
				$filepath = strtolower($filepath);
				if (file_exists($filepath)) {
					require_once $filepath;
					break;
				} else {
					$filepath = strtoupper($filepath);
					if (file_exists($filepath)) {
						require_once $filepath;
						break;
					} else {
						$success = FALSE;
					}
				}
			}
		}
		if (!$success) {
			trigger_error("Include file for the class '".$class."' not found.", E_USER_ERROR);
		}
	},
	TRUE,
	TRUE
);
?>
