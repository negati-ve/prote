<?php
namespace Json\Blueprints;
class Json{

	private $Data=NULL;
	private $Config=NULL;
	private $Head=NULL;

	public function set_data($data){
		$this->Data=json_encode($data);
	}

	public function set_head($head){
		$this->Head=$this->Data->$head;
	}

	public function reset_head(){
		$this->Head=$this->Data;
	}

	public function get_json_object(){
		return json_encode($this->Data);
	}
	
	public function get_json_pretty_object(){
		return json_encode($this->Data,JSON_PRETTY_PRINT);
	}

	public function is_json($data){
		json_decode($data);
 		return (json_last_error() == JSON_ERROR_NONE);
	}

	public function add_object($object){
		if(isset($this->Head->$object)){
			
			$this->Head=$this->Head->$object;
			return $this->Head;
		}
		$this->Head=$this->Head->$object=new \stdClass();
	}

	public function create_object($node){
		return $this->Head=$this->Data=new \stdClass();
	}

	public function add_property($property,$value){
		$this->Head->$property=$value; 
	}
}

?>