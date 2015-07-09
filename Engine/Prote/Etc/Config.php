<?php
namespace Etc;
use Etc\AutoLoaders\AutoLoad;
class Config{

	private $DebugMode=0;
	private $BasePath=NULL;
	private $DatabaseHost="localhost";
    private $DatabasePort="3306";
    private $DatabaseUser;
    private $DatabasePass;
    private $DatabaseName;
    private $AutoLoadFunction;
    private $DebugData;
    private $SystemStatus;


	public function __construct(){

	}

	public function get_basepath(){

		return realpath(__DIR__.'/../../../');
	
	}

	public function set_autoloader(Autoload $autoload,$auto_loader_function){

        spl_autoload_register(array($autoload,$auto_loader_function));

	}

	public function set_database_host($database_host="localhost"){
		$this->DatabaseHost=$database_host;
	}

	public function set_database_port($database_port="3306"){
		$this->DatabasePort=$database_port;
	}

	public function set_database_user($database_user){
		$this->DatabaseUser=$database_user;
	}

	public function set_database_pass($database_pass){
		$this->DatabasePass=$database_pass;
	}

	public function set_database_name($database_name){
		$this->DatabaseName=$database_name;
	}

	public function get_database_host(){
		return $this->DatabaseHost;
	}

	public function get_database_port(){
		return $this->DatabasePort;
	}

	public function get_database_user(){
		return $this->DatabaseUser;
	}

	public function get_database_pass(){
		return $this->DatabasePass;

	}

	public function get_database_name(){
		return $this->DatabaseName;
	}

	public function get_debug_mode(){
		return $this->DebugMode;
	}
	
	public function debug_mode_is_on(){
		return $this->DebugMode;
	}

	public function debug_mode_is_off(){
		return !$this->DebugMode;
	}

	public function start_debug(){
		ini_set('display_errors', 'On');
    	error_reporting(E_ALL);
		return $this->DebugMode=1;
	}

	public function stop_debug(){
		ini_set('display_errors', 'On');
    	error_reporting(0);
		return $this->DebugMode=0;
	}

	public function collect_debug_data($data){
		$this->DebugData.=$data;
	}

	public function get_debug_data(){
		return $this->DebugData;
	}


}

?>