<?php
namespace MiniCMS;

/**
 * Entry point
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */

/**
 * Debug settings
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * Get base directory
 */
$baseDirectory = dirname(__FILE__, 2);

/**
 * Include bootstrap
 */
include $baseDirectory.DIRECTORY_SEPARATOR.'application'.DIRECTORY_SEPARATOR.'bootstrap.php';

/**
 * Start application
 * 
 * @param string $baseDirectory Base directory
 * 
 * @return Application Instance of Application
 */
$application = new Application($baseDirectory);
?>
