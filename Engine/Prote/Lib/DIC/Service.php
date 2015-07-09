<?php
namespace DIC;
use Etc\Config;
// DIC\Blueprints\Loader;
class Service extends Blueprints\Loader {
	
	public function Curl(){
		return $this->load('Curl','\Curl\Curl');
	}

	public function CsvParser(){
		return $this->load('CsvParser','\KzykHys\CsvParser');
	}


	public function Upload(){
		return $this->load('Upload','\Upload\UploadInterface');
	}

	public function Curl_Websocket_Client(){
		return $this->load('Curl_Websocket_Client','\Curl\WebSocketClient\Client');
	}

	public function Config(){
		// var_dump( $this->Service['Config']);
		return $this->Service['Config'];
	}

	public function Router(){
		return $this->load('Router','\Bramus\Router');
	}

	public function Database(){
		return $this->load('Database','\Database\Database');
	}

	public function Was(){
		return $this->load('Was','\Was\WAS');
	}


	public function DbSession(){
		if($this->available('DbSession'))
			return $this->Service['DbSession'];
		else
			$this->Service['DbSession']=new \Sessions\DbSession($this);
			session_set_save_handler($this->Service['DbSession'],true);
			return $this->Service['DbSession'];

	}

	public function Auth(){
		return $this->load('Auth','\Prote\Objects\Auth');
	}

	public function Privilege(){
		return $this->load('Privilege','\User\Privilege');
	}

	public function session_start(){
		session_start();
		return session_id();
	}

	
	public function VAC(){
		return $this->load('VAC','\VA\VAC');

	}

	public function Truecaller(){
		return $this->load('Truecaller','\Truecaller\Truecaller');
	}

	public function VADB(){
		return $this->load('VADB','\Database\VADB');
	}

	public function Whatsapp(){
		return $this->load('Whatsapp','\Whatsapp\Whatsapp');
	}

	public function Json(){
		return $this->load('Json','\Json\Blueprints\Json');
	}

	public function DBlue(){
		return $this->load('DBlue','\Database\Blueprints\Wrapper');
	}

	public function Prote(){
		return $this->load('Prote','\Prote\ProteContainer');
	}

	public function Html(){
		return $this->load('Html','\Html\Html');
	}

	public function WebSocketClient()
	{
		return $this->load('WebSocketClient','\WebSocket\Client');
	}


	public function StanfordNLP(){
		return $this->load('StanfordNLP','\StanfordNLP\SNLPC');
	}

	public function HMI(){
		return $this->load('HMI','\HMI\HMIC');
	}

	public function enable_route($route){
		$Service=$this;
		$Router=$this->Router();
		$numargs = func_num_args();
		$args=func_get_args();
		foreach($args as $arg){
			include($this->Config()->get_basepath()."/Routes/".$arg.".php");
		}
		// $Router->run();
	}



}