<?php
namespace Prote;
use DIC\Service;
class ProteContainer {
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

	public function DBI(){
		return $this->load('DBI','\Prote\DBI\DBIC');
	}

	public function Objects(){
		return $this->load('Objects','\Prote\Objects\ProteObjectContainer');
	}

}