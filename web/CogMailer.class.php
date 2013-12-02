<?php

class CogMailer
{
	#handles sending email

	private $sender;
	private $recipient;
	private $replyAddr;
	private $msg = "";
	private $subject;
	private $headers;
	private $returnURL;
	private $openDiv = "<div>";
	private $closeDiv = "</div>";
	private $success;
	private $errormsg = "";

	public function __construct()
	{
		
	}

	public function setSender($email)
	{
		if (is_string($email) && !empty($email))
		{
			$this->sender = $email;
			if (!isset($this->replyAddr))
			{
				$this->replyAddr = $this->sender;
			}
			$this->configureHeaders();
		}
		return $this;
	}

	public function getSender()
	{
		return $this->sender;
	}

	public function getReplyAddress()
	{
		return $this->replyAddr;
	}

	public function setReplyAddress($email)
	{
		if (is_string($email) && !empty($email))
		{
			$this->replyAddr = $email;
		}
		else
		{
			if (isset($this->sender) && !empty($this->sender))
			{
				$this->replyAddr = $this->sender;
			}
			else
			{
				$this->replyAddr = "";
			}
		}
		
		$this->configureHeaders();
		return $this;
	}

	public function getRecipient()
	{
		return $this->recipient;
	}

	public function setRecipient($email)
	{
		if (isset($email) && !empty($email))
		{
			$this->recipient = $email;
			$this->configureHeaders();
		}
		return $this;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function setSubject($subj)
	{
		if (isset($subj) && !empty($subj))
		{
			$this->subject = $subj;
		}
		return $this;
	}

	public function getMessage()
	{
		return $this->msg;
	}

	public function setMessage($msg)
	{
		return $this->compose($msg);
	}

	public function getReturnUrl()
	{
		return $this->returnURL;
	}

	public function setReturnUrl($url)
	{
		if (isset($url) && !empty($url))
		{
			$this->returnURL = $url;
		}
		return $this;
	}

	private function configureHeaders()
	{
		$this->headers = "";
		$this->headers .= "MIME-Version: 1.0\r\n";
		$this->headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$this->headers .= "From: " . $this->sender . "\r\n";
		$this->headers .= "Reply-to: " . $this->replyAddr . "\r\n";
	}

	private function ready()
	{
		$c = isset($this->sender) && !empty($this->sender);
		$c = isset($this->replyAddr) && !empty($this->replyAddr);
		$c = $c && isset($this->recipient) && !empty($this->recipient);
		$c = $c && isset($this->headers) && !empty($this->headers);
		return $c;
	}

	private function checkMsg()
	{
		if (!isset($this->msg) || empty($this->msg))
		{
			return false;
		}
		return true;
	}

	private function checkSubj()
	{
		if (!isset($this->subj) || empty($this->subject))
		{
			return false;
		}
		return true;
	}

	public function compose($msg)
	{
		if (isset($msg) && !empty($msg))
		{
			//$this->msg .= $this->openDiv;
			$this->msg .= $msg;
			//$this->msg .= $this->closeDiv;
		}
		else
		{
			$this->msg = "";
		}
		return $this;
	}

//end compose()
//	private function sendPlus() {
//		//This function sends the mail and handles what happens thereafter, on success or failure.
//		//To have customized actions after mail is sent, use the SendMail() function instead.
//		if ($this->ready()) {
//			if ($this->checkSubj()) {
//				if ($this->checkMsg()) {
//					//$this->sendFlag = $this->sendMail();
//					if ($this->send()) {
//						$this->handleSendSuccess();
//					}
//					else {
//						$this->handleSendFailure();
//					}
//				}
//				else {
//					CogJs::alert("Returning for you to compose email message...");
//				}
//			}
//			else {
//				CogJs::alert("Returning for you to enter a subject for your email...");
//			}
//		}
//		else {
//			//isReady() returned false, meaning that the parameters required to send the mail are not complete, and thus the mail cannot be sent.
//			$this->handlePrematureMailError();
//		}
//	}

	/**
	 * Sends the mail and returns true or false on success.
	 * @return boolean true or false depending on success.
	 */
	public function send()
	{
		if ($this->ready())
		{
			if (mail($this->recipient, $this->subject, $this->msg, $this->headers))
			{
				$this->handleSendSuccess();
				return true;
			}
			else
			{
				$lastError = error_get_last();
				$this->handleSendFailure($lastError['message']);
				return false;
			}
		}
		else
		{
			//$this->handlePrematureMailError(); #do this??
			return false;
		}
	}

	private function handleSendSuccess()
	{
		$this->setSuccess(true);
	}

	private function handleSendFailure($errormsg)
	{
		$this->setSuccess(false);
		$this->errormsg = $errormsg;
	}

//	private function handlePrematureMailError() {
//		$error_message = "The mail is not ready to be sent. Some important parameters are either absent or incorrectly set. Please check and try again.";
//		CogJs::alert($error_message);
//	}

	private function setSuccess($bool_success)
	{
		$this->success = $bool_success;
	}

	public function ok()
	{
		return $this->success;
	}

	public function getErrorMsg()
	{
		return $this->errormsg;
	}

//	private function printOpenDiv() {
//		print $this->openDiv;
//	}
//
//	private function printCloseDiv() {
//		print $this->closeDiv;
//	}
//	private function displayMsg() {
//		$this->printOpenDiv();
//		print "<b>-------EMAIL DETAILS:-------</b><br>";
//		print "<br/>";
//		print "From: " . $this->sender;
//		print "<br/>";
//		print "To: " . $this->recipient;
//		print "<br/>";
//		print "Reply-to: " . $this->replyAddr;
//		print "<br/>";
//		print $this->headers;
//		print "<br/><br/>";
//		print $this->msg;
//		$this->printCloseDiv();
//	}
//end displayMsg()
}

//end class CogMailer
?>