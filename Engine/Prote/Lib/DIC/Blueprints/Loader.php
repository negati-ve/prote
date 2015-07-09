<?php
namespace DIC\Blueprints;
use Etc\Config;
abstract class Loader {
	protected $Service=array();
	// private $Data=array();
	public function __construct(Config $Config){
		$this->Service['Config']=$Config;
		// var_dump($this->Service['Config']);
	}

	public function available($Class){
		if(isset($this->Service[$Class]))
			return true;
		else
			return false;
	}

	public function load($name,$path){
		if($this->available($name))
			return $this->Service[$name];
		else{
			return $this->Service[$name]=new $path($this);
		}
	}

	public function load_new($name,$path){
		return new $path($this);
	}

	public function manual_load($class){
		$a=explode('\\', $class);
		$ClassName=$a[count($a)-1];
		if($this->available($ClassName))
			return $this->Service[$ClassName];
		else
			return $this->Service[$ClassName]=new $class($this);
	}

	public function attach_as_sub_service_provider($name,$class){
		//EXPERIMENTAL.
		if($this->available($name))
			return 0;
		else{
			return $this->Service[$name]=$class;
		}
	}
}