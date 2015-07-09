<?php
namespace Prote\Objects;
use DIC\Service;
class Mailer{
	private $Service;
	private $Mailer;
	public function __construct(Service $Service){
		$this->Service=$Service;
		$this->Db=$this->Service->Database();
		$this->Mailer = new \PHPMailer\PHPMailer;
	}

	public function use_mailgun(){
		$this->Mailer->isSMTP();  // Set mailer to use SMTP
		$this->Mailer->Host = 'smtp.mailgun.org';  // Specify mailgun SMTP servers
		$this->Mailer->SMTPAuth = true; // Enable SMTP authentication
		$this->Mailer->Username = 'postmaster@sandbox9841d4cc7b394a59bbdb3024c6d1a088.mailgun.org'; // SMTP username from https://mailgun.com/cp/domains
		$this->Mailer->Password = '8be970bfde8f8ff1d5061406734e3a35'; // SMTP password from https://mailgun.com/cp/domains
		$this->Mailer->SMTPSecure = 'tls';   // Enable encryption, 'ssl'
		$this->Mailer->setFrom('postmaster@sandbox9841d4cc7b394a59bbdb3024c6d1a088.mailgun.org', 'Reset');
				//Set an alternative reply-to address
		$this->Mailer->addReplyTo('ahabba15@gmail.com', '#Habba15');

	}

	public function activate_debug($level=1){
		$this->Mailer->SMTPDebug  = $level;    
	}
	public function use_va(){
		$this->Mailer->isSMTP();  // Set mailer to use SMTP
		$this->Mailer->Host = 'smtp.mailgun.org';  // Specify mailgun SMTP servers
		$this->Mailer->SMTPAuth = true; // Enable SMTP authentication
		$this->Mailer->Username = 'acharya-habba@vtuacademy.com'; // SMTP username from https://mailgun.com/cp/domains
		$this->Mailer->Password = '#Habba15'; // SMTP password from https://mailgun.com/cp/domains
		$this->Mailer->SMTPSecure = 'tls';   // Enable encryption, 'ssl'
		$this->Mailer->setFrom('acharya-habba@vtuacademy.com', '#Habba15');
				//Set an alternative reply-to address
		$this->Mailer->addReplyTo('acharya-habba@vtuacademy.com', '#Habba15');

	}

	public function attach($path,$name){
		$this->Mailer->AddAttachment($path, $name);

	}


	public function send($email,$subject,$html){
		$this->Mailer->addAddress($email);
		$this->Mailer->Subject = $subject;
		$this->Mailer->IsHTML(true);
		$this->Mailer->Body=$html;
		if (!$this->Mailer->send()) {
			return 0;
		} else {
			return 1;
		}
	}
}