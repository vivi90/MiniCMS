<?php
namespace MiniCMS;

/**
 * Mail class
 *
 * @package MiniCMS
 * @license https://opensource.org/licenses/MIT The MIT License
 * @author Vivien Richter <vivien-richter@outlook.de>
 */
class Mail
{	
	/**
	 * @var string Recipient(s) (must comply with RFC 2822)
	 */
	private $recipients;
	
	/**
	 * @var string Subject
	 */
	private $subject;
	
	/**
	 * @var string Message
	 */
	private $message;
	
	/**
	 * @var string Character set
	 */
	private $charset;
	
	/**
	 * Creates a new class instance
	 * 
	 * @param string $recipients Recipient(s) (must comply with RFC 2822)
	 * @param string $subject Subject
	 * @param string $message Message
	 * @param string $charset Character set (default: utf-8)
	 */
 	public function __construct($recipients, $subject, $message, $charset = "utf-8")
	{
		$this->recipients = $recipients;
		$this->subject = $subject;
		$this->message = $message;
		$this->charset = $charset;
	}
	
	/**
	 * Generate email header
	 * 
	 * @param string $charset Character set (default: utf-8)
	 * 
	 * @return string Email header
	 */
	private function generateHeader($charset = "utf-8")
	{
		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: text/html; charset=".$charset."\r\n";
		return $header;
	}
	
	/**
	 * Send email
	 * 
	 * @return bool Returns TRUE if the mail was successfully accepted for delivery, FALSE otherwise.
	 */
	public function send()
	{
		return mail(
			$this->recipients, 
			$this->subject, 
			$this->message,
			$this->generateHeader($this->charset)
		);
	}
}
