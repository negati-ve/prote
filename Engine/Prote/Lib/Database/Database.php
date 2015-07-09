<?php

namespace Database;
use Database\Blueprints\Database as Database_Blueprint,
DIC\Service;
class Database extends Database_Blueprint{

	private $result=NULL;
	private $Container;

	public function __construct(Service $Service){
		$this->Service=$Service;
		$this->Config=$this->Service->Config();
		parent::__construct();
	}

	public function backup($debug=0){
		$dbhost=$this->Service->Config()->get_database_host();
		$dbname=$this->Service->Config()->get_database_name();
		$dbuser=$this->Service->Config()->get_database_user();
		$dbpass=$this->Service->Config()->get_database_pass();
		$base=$this->Service->Config()->get_basepath();
		
		$backup_file = $dbname . date("Y-m-d-H-i-s") . '.gz';
		$backup_name= $base."/Static/VA/Backup/".$backup_file;
		$command = "mysqldump --opt -h $dbhost -u $dbuser -p$dbpass ".
           "$dbname | gzip > $base/Static/VA/Backup/$backup_file";
		system($command);
		$Mailer=$this->Service->Prote()->Objects()->Mailer();
		$Mailer->use_mailgun();
		$Mailer->attach($backup_name,$backup_file);
		if($debug){
			$Mailer->activate_debug();
		}
		$Mailer->send('habbadbbackup@acharya.ac.in','Confidential. [Habba-Database]','Habba Database Backup. ');
		echo "Payload mailed .<br>";
		$command= unlink($backup_name);
		echo "Payload deleted .";
	}

	public function drop_payload($payloads,$class=NULL){
		if($class==NULL){
			$caller=get_class();
		}else{
			$caller=get_class($class);	
		}
		$i=1;
		// $caller=get_called_class();
		foreach($payloads as $payload){
			// var_dump($payload);
			echo "Dropping payload ".$i." for ".$caller.".";
			$this->query($payload);
			echo "<br>";
			$i++;
		}
	}

	public function check_dependencies($payloads,$class=NULL){
		if($class==NULL){
			$caller=get_class();
		}else{
			$caller=get_class($class);	
		}
		$i=1;
		foreach($payloads as $payload){
			echo "checking dependency ".$payload." for ".$caller.".";
			$this->set_parameters(array($this->Config->get_database_name(),$payload));
			if($this->find_one('SELECT TABLE_NAME as T FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1 ')->T){
				echo "Dependency met.<br>";
			}else{
				echo "Dependency not met.<br>Prote Out.";
				exit();
			}
			echo "<br>";
			$i++;
		}
	}


	public function get_result_by_usn($usn){
		if ($this->result!=NULL && $this->result->usn_code==$usn){
			return $this->result;
		}
		$this->connect();
		$this->set_parameters(array($usn));
		// $result=$this->query("Select * FROM exam_Va WHERE usn_code=?");
		$result=$this->find_one("Select * FROM exam_Va WHERE usn_code=?");
		return $result;
	}

	public function get_name_by_usn($usn){
		$this->connect();
		$this->set_parameters(array($usn));
		$result=$this->query("Select st_name FROM student WHERE usn_code=?");
		// var_dump($result);
		return $result->st_name;
	}

	
}

?>
