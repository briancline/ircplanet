<?php

	/**
	 * Abstract Email wrapper class. Currently uses PHPMailer, 
	 * found in the Core/lib/phpmailer directory.
	 * 
	 * Can easily be modified to use different mailers should you need one.
	 */
	require_once(CORE_LIB_DIR .'phpmailer/class.phpmailer.php');
	
	class Email {
		private $mailer = null;
		
		function __construct() {
			$this->mailer = new PHPMailer();
			$this->mailer->IsSMTP();
			$this->mailer->Host = SMTP_HOST;
			$this->mailer->Port = SMTP_PORT;
			$this->mailer->Username = SMTP_USER;
			$this->mailer->Password = SMTP_PASS;
		}
		
		
		public function addAddress($address, $name = '')  { return $this->mailer->AddAddress($address, $name); }
		public function addCC($address, $name = '')       { return $this->mailer->AddCC($address, $name); }
		public function addBCC($address, $name = '')      { return $this->mailer->AddBCC($address, $name); }
		public function setFrom($address, $name = '')     { return $this->mailer->SetFrom($address, $name); }
		public function setSubject($subject)              { $this->mailer->Subject = $subject; }
		public function setBody($plainText)               { $this->mailer->Body = $plainText; }
		
		public function send()     { return $this->mailer->Send(); }
		public function getError() { return $this->mailer->ErrorInfo; }
	}
