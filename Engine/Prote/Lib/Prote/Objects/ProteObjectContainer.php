<?php
namespace Prote\Objects;
use DIC\Service;
class ProteObjectContainer {
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

	public function Auth(){
		return $this->load('Auth','\Prote\Objects\Auth');
	}

	public function Mailer(){
		return $this->load('Mailer','\Prote\Objects\Mailer');
	}
}
