<?php
namespace User;
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
		$this->generate_hash($pwd);
		$this->Db->set_parameters(array($email,$this->hash));
		if($data=$this->Db->find_one('SELECT id,privilege from '.$this->DbName.' where email=? && pwd=?')){
			$_SESSION['id']=$data->id;
			$this->Service->Privilege()->set($data->privilege);
			return true;
		}else{
			return false;
		}
		
		
	}

	public function login_debug(){
		$status=$this->Service->Auth()->login($_POST['email'],$_POST['password'],$_POST['auth_token']);
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
		$this->generate_hash($pwd);
		$this->Db->set_parameters(array($email,$this->hash,7));
		$this->Db->insert('Insert into '.$this->DbName.' SET email=?,pwd=?,privilege=?,created=NOW()');
		if($login==1)
			$_SESSION['id']=$this->Db->last();
		return true;
		}
	}

	public function email2pwd($email){
		$this->Db->set_parameters(array($email));
		return $this->Db->find_one('SELECT pwd from '.$this->DbName.' where email=?')->pwd;
	}

	public function generate_hash($pwd){
		return $this->hash=hash('sha512',$pwd.$this->salt);
	}

	public function make_salt($email,$pwd){
		return $this->salt=crypt($pwd, $email.'5UCK5'.$pwd);	
	}

	public function user_exists($email){
		$this->Db->set_parameters(array($email));
		$a=$this->Db->find_one('Select count(*) as count from '.$this->DbName.' where email=?');
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

	public function install(){
		$payload1="CREATE TABLE IF NOT EXISTS `ProtePeople` (
`Id` int(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `college` int(11) NOT NULL,
  `inviter` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `last_seen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` tinyint(1) NOT NULL,
  `privilege` tinyint(4) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `DOB` date NOT NULL,
  `gender` varchar(2) NOT NULL,
  `invite_code` tinyint(1) NOT NULL DEFAULT '5',
  `whatsapp` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `story` text NOT NULL,
  `logins` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
";
	$payload2="ALTER TABLE `ProtePeople`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `email` (`email`);";
	$payload3="ALTER TABLE `ProtePeople`
MODIFY `Id` int(255) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=0;";

	$payloads=array($payload1,$payload2,$payload3);
	$this->Db->drop_payload($payloads,$this);
	}

}
?>