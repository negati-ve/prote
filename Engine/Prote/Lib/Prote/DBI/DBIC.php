<?php
//DBI = DATABASE INTERFACE
//DBIC = DATABASE INTERFACE CONTAINER.
namespace Prote\DBI;
use DIC\Service;
class DBIC {
	private $Storage=array();
	private $Service;

	public function __construct(Service $Service){
		$this->Service=$Service;
	}

	public function available($class){
		if(isset($this->Storage[$class]))
			return true;
		else
			return false;
	}

	public function load($name,$path){
		if($this->available($name))
			return $this->Storage[$name];
		else
			return $this->Storage[$name]=new $path($this->Service);
	}

	public function People(){
		return $this->load('People','\Prote\DBI\People\ProtePeopleContainer');
	}

	public function Community(){
		return $this->load('Community','\Prote\DBI\Community\ProteCommunityContainer');
	}
}