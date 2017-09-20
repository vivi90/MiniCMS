<?php
namespace MiniCMS;

/**
 * ErrorHandler class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
 class ErrorHandler
 {
 	/**
	 * @var bool Debug mode
	 */
	private $debug;
	
	/**
	 * @var string Webmaster name
	 */
	private $webmater;
	
	/**
	 * @var string Webmaster email address
	 */
	private $webmasterEmail;
	
	/**
	 * @var string Project name
	 */
	private $project;
	
 	/**
	 * @var mixed Error code
	 */
	private $errorCode;
	
	/**
	 * @var string Error message
	 */
	private $errorMessage;
	
	/**
	 * @var string Filename
	 */
	private $filename;
	
	/**
	 * @var int Line number
	 */
	private $line;
	
	/**
	 * @var array Context
	 */
	private $context;
	
	/**
	 * @var string Character set
	 */
	private $charset;
	
 	/**
	 * Creates a new class instance
	 * 
	 * @param bool $debug Debug mode
	 * @param string $webmaster Webmaster name
	 * @param string $webmasterEmail Webmaster email address
	 * @param string $project Project name
	 * @param string $charset Character set
	 */
 	public function __construct($debug, $webmaster, $webmasterEmail, $project, $charset = "utf-8")
	{
		$this->debug = $debug;
		$this->webmater = $webmaster;
		$this->webmasterEmail = $webmasterEmail;
		$this->project = $project;
		$this->charset = $charset;
		set_error_handler(array($this, 'catchError'));
		set_exception_handler(array($this, 'catchError'));
	}
	
	/**
	 * Catch error or exception
	 */
	public function catchError()
	{
		// Checks if it is a Exception or a Error
		if (func_num_args() == 1) {
			$exception = func_get_arg(0);
			$this->errorCode = $exception->getCode();
			$this->errorMessage = $exception->getMessage();
			$this->filename = $exception->getFile();
			$this->line = $exception->getLine();
			$this->context = $exception->getTrace();
		} else {
			$this->errorCode = func_get_arg(0);
			$this->errorMessage = func_get_arg(1);
			$this->filename = func_get_arg(2);
			$this->line = func_get_arg(3);
			$this->context = func_get_arg(4);
		}
		// Generate report
		$report = $this->generateReport(
			$this->errorCode, 
			$this->errorMessage, 
			$this->filename, 
			$this->line, 
			$this->context, 
			$this->charset
		);
		// Display or send report
		if ($this->debug) {
			die($report);
		} else {
			$this->sendReport(
				$this->webmater.' <'.$this->webmasterEmail.'>', 
				'Error in '.$this->project, 
				$report, 
				$this->charset
			);
			die(
				$this->generateUserMessage(
					$this->webmasterEmail, 
					$this->charset
				)
			);
		}
	}
	
	/**
	 * Generate error report
	 * 
	 * @param mixed $errorCode Error code
	 * @param string $errorMessage Error message
	 * @param string $filename Filename
	 * @param int $line Line number
	 * @param array $context Context
	 * @param string $charset Character set (default: utf-8)
	 * 
	 * @return string Error report
	 */
	private function generateReport($errorCode, $errorMessage, $filename, $line, $context, $charset = "utf-8")
	{
		return '<!DOCTYPE html>
<html lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset='.$charset.'"/>
  <title>Error</title>
 </head>
 <body>
  <header>
   <h1>Error</h1>
  </header>
  <table>
    <tr>
      <th>Level</th>
      <td>'.$errorCode.'</td>
    </tr>
    <tr>
      <th>Message</th>
      <td>'.$errorMessage.'</td>
    </tr>
    <tr>
      <th>File</th>
      <td>'.$filename.'</td>
    </tr>
    <tr>
      <th>Line</th>
      <td>'.$line.'</td>
    </tr>
    <tr>
      <th>Context</th>
      <td>'.var_export($context, TRUE).'</td>
    </tr>
  </table>
 </body>
</html>';
	}
	
	/**
	 * Generate error message for the user
	 * 
	 * @param string $webmasterEmail Webmaster email address
	 * @param string $charset Character set (default: utf-8)
	 * 
	 * @return string User message
	 */
	private function generateUserMessage($webmasterEmail, $charset = "utf-8")
	{
		return '<!DOCTYPE html>
<html lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset='.$charset.'"/>
  <title>Error</title>
 </head>
 <body>
  <header>
   <h1>An error occured.</h1>
  </header>
  <p>I am sorry for the inconvenience. I have been notified and will correct this issue as quickly as possible. For further information, please contact me at <a href="mailto:'.$webmasterEmail.'">'.$webmasterEmail.'</a>.</p>
 </body>
</html>';
	}
	
	/**
	 * Send report as eMail
	 * 
	 * @param string $recipients Recipient(s) (must comply with RFC 2822)
	 * @param string $subject Subject
	 * @param string $report Error report
	 * @param string $charset Character set (default: utf-8)
	 * 
	 * @return bool Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	 */
	private function sendReport($recipients, $subject, $report, $charset = "utf-8")
	{
		$mail = new Mail(
			$recipients, 
			$subject, 
			$report, 
			$charset
		);
		return $mail->send();
	}
}
?>
