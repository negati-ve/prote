<?php
namespace User;
use DIC\Service;
class Privilege{
	
	private $Service;
	private $id;
	public function __construct(Service $Service){
		// echo "pro";
		$this->Service=$Service;
		$this->Service->Auth();
		if(isset($_SESSION['p'])){
			$this->id=$_SESSION['p'];
			$this->visitor();
		}
		else{
			return $this->passerby();		
		}
		return true;

	}

	public function set($id){
		$this->id=$_SESSION['p']=$id;
	}

	public function get(){
		return $this->id;
	}

	public function visitor(){
		if($this->id==9){
			// $this->Service->Database()->connect();
			$this->Service->Database()->query('UPDATE Analytics SET visitors=visitors+1');
			return true;
		}
}

	public function passerby(){
			$_SESSION['p']=9;
			// $this->Service->Database()->connect();
			$this->Service->Database()->query('UPDATE Analytics SET passerbys=passerbys+1');
			return true;
	}

	public function root(){
		if($this->id==1)
			return true;
		else
			return false;		
	}

	public function admin(){
		if($this->id<=4)
			return true;
		else
			return false;	
	}

	public function basic(){
		if($this->id<=7){
			// $this->Service->Database()->connect();
			$this->Service->Database()->query('UPDATE Analytics SET users=users+1');
			return true;
		}
		else
			return false;	
	}

	public function exclusive(){
		if($this->id<=6){
			// $this->Service->Database()->connect();
			$this->Service->Database()->query('UPDATE Analytics SET users=users+1');
			return true;
		}
		else
			return false;	
	}

	public function install(){
		$payload="
CREATE TABLE IF NOT EXISTS `Analytics` (
  `passerbys` int(11) NOT NULL,
  `visitors` int(11) NOT NULL,
  `users` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
	$payloads=array($payload);
	$this->Service->Database()->drop_payload($payloads);
	}



}
?>
