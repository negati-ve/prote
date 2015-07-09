<?php
namespace Prote\Objects;
use DIC\Service;
class Auth{
	private $salt='';
	private $hash='';
	private $AuthToken=NULL;
	private $Service;
	private $DbName='ProtePeople';
	public function __construct(Service $Service){
		$this->Service=$Service;
		$this->Service->DbSession();
		$this->Db=$this->Service->Database();
		$this->Db->connect();
		$this->setup();
	}

	public function login($email,$pwd,$auth_token){
		$email=strtolower($email);
		if($this->auth_token_verification_fails($auth_token)){
			if($this->Service->Config()->debug_mode_is_on()){
				$this->Service->Config()->collect_debug_data('<br>Could not verify auth token');
				var_dump($this->Service->Config()->get_debug_data());
			}
			return false;
		}
		$this->make_salt($email,$pwd);
		$pwd=$this->generate_hash($pwd);

		// $this->Db->set_parameters(array($email,$this->hash));
		// if($data=$this->Db->find_one('SELECT id,privilege from '.$this->DbName.' where email=? && pwd=?')){
		if($id=$this->Service->Prote()->DBI()->People()->Main()->verify($email,$pwd)){	
			$_SESSION['id']=$id;
			// $this->Service->Privilege()->set($data->privilege);
			return true;
		}else{
			return false;
		}
	}

	public function login_debug(){
		$email=$_POST['email'];
		$pwd=$_POST['pwd'];
		$auth_token=$_POST['auth_token'];
		$this->make_salt($email,$pwd);
		$pwd=$this->generate_hash($pwd);
		$status=$this->Service->Prote()->DBI()->People()->Main()->verify($email,$pwd,$auth_token);
		var_dump($status);
		exit();
	}

	public function logout($auth_token){
		if($this->auth_token_verification_fails($auth_token))
			return false;

		if(!isset($_SESSION['id'])){
			return false;
		}
		else{
			// $this->Service->DbSession()->destroy($_SESSION['id']);
			session_destroy();
			$this->setup();
			return true;
		}
	}

	public function register($email,$pwd,$login=0,$session_token=NULL){
		$email=strtolower($email);
		if( strlen($pwd) < 8 ) {
			$this->Service->Html()->Error='Password less than 8 characters';
		     return false;
		}
		if($this->auth_token_verification_fails($session_token) && $session_token!=NULL){
			$this->Service->Html()->Error='Auth token verificaion failed';
			return false;
		}
		if($this->user_exists($email))
		{
			return false;
		}
		else
		{
		$this->make_salt($email,$pwd);
		$hash=$this->generate_hash($pwd);
		// $this->Db->set_parameters(array($email,$this->hash,7));
		// $this->Db->insert('Insert into '.$this->DbName.' SET email=?,pwd=?,privilege=?,created=NOW()');
		$uid=$this->Service->Prote()->DBI()->People()->Main()->add($email,$hash);
		if($login==1)
			$_SESSION['id']=$this->Db->last();
		if($uid)
			return $uid;
		else
			return 0;
		}
	}

	public function password_reset_token_generated($email,$auth_token){
		return $this->generate_reset_password_token($email,$auth_token);
	}

	public function generate_reset_password_token($email,$auth_token){
		$email=strtolower($email);
		if($this->auth_token_verification_fails($auth_token)){
			if($this->Service->Config()->debug_mode_is_on()){
				$this->Service->Config()->collect_debug_data('<br>Could not verify auth token');
				var_dump($this->Service->Config()->get_debug_data());
			}
			return false;
		}
		$token=$this->generate_hash($email."#H4bb415");
		$this->Service->Prote()->DBI()->People()->ResetMap()->add($this->email2id($email),1,$token);
		$mail = new \PHPMailer\PHPMailer;
		// Set PHPMailer to use the sendmail transport
		// $mail->isSendmail();

		// $mail->IsSMTP(); // telling the class to use SMTP
		// $mail->Host       = "mail.acharyahabba.in"; // SMTP server
		// $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->isSMTP();  // Set mailer to use SMTP
$mail->Host = 'smtp.mailgun.org';  // Specify mailgun SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'postmaster@sandbox9841d4cc7b394a59bbdb3024c6d1a088.mailgun.org'; // SMTP username from https://mailgun.com/cp/domains
$mail->Password = '8be970bfde8f8ff1d5061406734e3a35'; // SMTP password from https://mailgun.com/cp/domains
$mail->SMTPSecure = 'tls';   // Enable encryption, 'ssl'


		// $mail->SMTPAuth   = true;                  // enable SMTP authentication
		// $mail->SMTPSecure = "tls";
		// $mail->Host       = "mail.acharyahabba.in"; // sets the SMTP server
		// $mail->Host       = "smtp.gmail.com"; 
		// $mail->Port       = 465;                    // set the SMTP port for the GMAIL server
		// $mail->Username   = "ahabba15@gmail.com"; // SMTP account username
		// $mail->Password   = "ZASwastik";

		//Set who the message is to be sent from
		$mail->setFrom('postmaster@sandbox9841d4cc7b394a59bbdb3024c6d1a088.mailgun.org', 'Reset');
		//Set an alternative reply-to address
		$mail->addReplyTo('ahabba15@gmail.com', 'habba');
		//Set who the message is to be sent to
		$mail->addAddress($email);
		//Set the subject line
		$mail->Subject = 'Password Reset- #Habba15';
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->IsHTML(true);
		$reset_url='http://acharyahabba.in/password-reset-verify/'.$email.'/'.$token;
		$reset_link='<a href="'.$reset_url.'">Click here</a><br><br>';
		$mail->Body='We have recieved a request to reset your password.  Please follow this link - '.$reset_link.' <br> OR copy paste this url in your browser - '.$reset_url.'<br> Thank you. ';
		//send the message, check for errors
		if (!$mail->send()) {
		//echo "Mailer Error: " . $mail->ErrorInfo;
		return 0;
		} else {
		//echo "Message sent!";
		return 1;
		}
	}

	public function verify_reset_password_token($email,$token){
		//SECURITY ALERT 
		//THIS EXPOSES THE RESET KEY. CAN BE REUSED IF LEAKED OR SESSION HIJACKED.
			if($token==$this->generate_hash($email."#H4bb415")){
				return 1;
			}
	}

	public function reset_password($email,$pwd,$session_token,$login=0){
		$email=strtolower($email);
		$uid=$this->email2id($email);

		if(!$this->Service->Prote()->DBI()->People()->ResetMap()->exists($uid)){
			
			$this->Service->Html()->Error='Illegal attempt to reset password.';
			return false;
		}
		$this->Service->Prote()->DBI()->People()->ResetMap()->remove($uid);
		if( strlen($pwd) < 8 ) {
			$this->Service->Html()->Error='Password less than 8 characters';
		     return false;
		}
		if($this->auth_token_verification_fails($session_token) && $session_token!=NULL){
			$this->Service->Html()->Error='Auth token verificaion failed';
			return false;
		}
		if($this->user_exists($email))
		{
			$this->make_salt($email,$pwd);
			$hash=$this->generate_hash($pwd);
			// $this->Db->set_parameters(array($email,$this->hash,7));
			// $this->Db->insert('Insert into '.$this->DbName.' SET email=?,pwd=?,privilege=?,created=NOW()');
			$uid=$this->Service->Prote()->DBI()->People()->Main()->reset_password($email,$hash);
			if($login==1)
				$_SESSION['id']=$this->Db->last();
			if($uid)
				return $uid;
			else
				return 1;
		}
		else
		{
			return false;
		}
	}

	public function email2pwd($email){
		$this->Db->set_parameters(array($email));
		return $this->Db->find_one('SELECT pwd from '.$this->DbName.' where Email=?')->Pwd;
	}

	public function email2id($email){
		$this->Db->set_parameters(array($email));
		return $this->Db->find_one('SELECT Id from '.$this->DbName.' where Email=?')->Id;
	}

	public function generate_hash($pwd){
		return $this->hash=hash('sha512',$pwd.$this->salt);
	}

	public function make_salt($email,$pwd){
		return $this->salt=crypt($pwd, $email.'5UCK5'.$pwd);	
	}

	public function user_exists($email){
		$this->Db->set_parameters(array($email));
		$a=$this->Db->find_one('Select count(*) as count from '.$this->DbName.' where Email=?');
		if($a->count==1)
			return true;
		else
			return false;
	}

	public function generate_auth_token(){
		return $_SESSION['auth_token']=$this->Service->Html()->auth_token=uniqid(mt_rand());
	}

	public function generate_random_key(){
		return uniqid(mt_rand());
	}

	public function get_auth_token(){
		if(isset($_SESSION['auth_token']))
			$this->Service->Html()->auth_token=$_SESSION['auth_token'];
		else
			$this->generate_auth_token();
	}

	public function auth_token_verified($auth_token){
		if(!empty($auth_token) && $_SESSION['auth_token']==$auth_token ){
			$this->generate_auth_token();
			return true;
		}
		else {
			return false;
		}
	}

	public function auth_token_verification_fails($auth_token){
		if(empty($auth_token) || $_SESSION['auth_token']!==$auth_token ){
			return true;
		}
		else {
			$this->generate_auth_token();
			return false;
		}
	}

	public function setup($name="PHPSESSID"){
		if(isset($_COOKIE[$name])){
			if(empty($_COOKIE[$name])){
            	// $this->Service->DbSession()->session_repair();  
            	session_start();
                session_destroy();
                session_start();  
                session_regenerate_id();
                $this->generate_auth_token();
                return session_id();
            
			}
			else{
				session_start();
				$this->get_auth_token();

			}
		}else{
			session_start();
			$this->generate_auth_token();
		}
	}

	public function logged_out(){
		if(isset($_SESSION['id']))
			return false;
		else
			return true;
	}
	
	public function logged_in(){
		if(isset($_SESSION['id']))
			return true;
		else
			return false;
	}

	public function get_uid(){
		return $_SESSION['id'];
	}

	public function get_my_first_name(){
		$uid=$this->get_uid();
		$this->Db->set_parameters(array($uid));
		return $this->Db->find_one("SELECT first_name as fn FROM ProtePeople WHERE Id = ?")->fn;
	}

	public function verify_session($name="PHPSESSID"){

		

	}

}
?>