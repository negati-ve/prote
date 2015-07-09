<?php

namespace Database\Blueprints;
use Database\Blueprints\Database as Database_Blueprint,
DIC\Service;
class Wrapper extends Database_Blueprint{

	private $result=NULL;
	private $Container;
	

	public function __construct(Service $Service){
		$this->Service=$Service;
		$this->Config=$this->Service->Config();
		parent::__construct();
	}

	public function cd($arg=NULL){
		if($arg==NULL){
			$_SESSION['DbShellPWD']=$this->Config->get_database_name();
			return 1;
		}
		elseif($arg==".." ){

		}
		elseif(!isset($_SESSION['DbShellPWD'])){
			$_SESSION['DbShellPWD']='/'.$this->Config->get_database_name().'/'.$arg;
			return 1;
		}

	}

	public function pwd(){
		return $_SESSION['DbShellPWD'];
	}

	public function ls($arg=NULL){
		if(!isset($_SESSION['DbShellPWD'])){
			$_SESSION['DbShellPWD']=$this->Config->get_database_name();
			return $this->lstb();
		}
		elseif($_SESSION['DbShellPWD']==$this->Config->get_database_name()){
			return $this->lstb();
		}
		elseif($_SESSION['DbShellPWD']=='/'){
			
			// return $this->lscl($arg);
			return $this->lsdb();
		}else{
			$tb=(explode("/",$_SESSION['DbShellPWD'])); 
			$tb=end($tb);
			return $this->lscl($tb);
			}
		
	}

	public function lsdb(){
		return $this->find_many("SHOW DATABASES;");
	}

	public function use_db($Db){
		$this->set_parameters(array($Db));
		return $this->query("use ?");
	}

	public function lstb(){
		$this->set_parameters(array($this->Config->get_database_name()));
		return $this->find_many("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=?");
	}

	public function get_current_db(){
		return $this->find_one("SELECT DATABASE() as db FROM DUAL;")->db;
	}

	public function lscl($table){
		// $this->set_parameters(array($table));
		$statement="DESCRIBE ".$table;
		$stmt2="select *
from information_schema.referential_constraints
where constraint_schema = 'habba.dev' and TABLE_NAME='ProtePeopleMeta'";
		return $this->find_many($statement);
	}

	public function select_table($table){

	}

	public function table_exists($table){
		$tabless=$this->lstb();
		foreach($tabless as $tables){
			foreach($tables as $name){
				if($name==$table)
					return 1;
			}
		}
		return 0;
	}

}

?>