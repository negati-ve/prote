<?php
namespace Curl;
use DIC\Service;

class Curl{
	private $CurlObject=NULL;
	private $CurlDebug=0;
	private $CurrentHandle;
	private $CurrentOutput;
	private $Service;
	private $Method=NULL;
	private $CurrentUrl=NULL;

	public function __construct(Service $Service){
		$this->Service=$Service;
	}

	public function ping($domain){
		if(!filter_var($domain, FILTER_VALIDATE_URL)){
			// echo "not found";
			return false;
		}
		$this->CurlObject = curl_init($domain);
		curl_setopt($this->CurlObject,CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($this->CurlObject,CURLOPT_HEADER,true);
		curl_setopt($this->CurlObject,CURLOPT_NOBODY,true);
		curl_setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,true);
		$response = curl_exec($this->CurlObject);
		curl_close($this->CurlObject);
		if($response) 
			return true;
		return false;
	}

	public function init(){
		$this->CurlObject=curl_init();
	}

	public function current(){
		return $this->CurlObject;
	}


	public function get_current_output(){
		return $this->CurrentOutput;
	}

	public function set_readfunction($function){
		$this->setopt($this->CurlObject,CURLOPT_READFUNCTION,$function);		
	}

	public function set_writefunction($function){
		$this->setopt($this->CurlObject,CURLOPT_WRITEFUNCTION,$function);		
	}

	public function setopt(){
		call_user_func_array('curl_setopt', func_get_args());
	}

	public function set_url($url){
		$this->setopt($this->CurlObject,CURLOPT_URL,$this->CurrentUrl=$url);
	}

	public function set_reuse($value){
		$this->setopt($this->CurlObject,CURLOPT_FORBID_REUSE,!$value);
	}

	public function get_url(){
		return $this->CurrentUrl;
	}

	public function set_cookie_jar($path){
		$this->setopt($this->CurlObject,CURLOPT_COOKIEJAR,$path);
	}

	public function set_cookie($value){
		$this->setopt($this->CurlObject,CURLOPT_COOKIE,$value);
	}

	public function set_header($value){
		$this->setopt($this->CurlObject,CURLOPT_HEADER,$value);
	}

	public function set_httpheader($array){
		$this->setopt($this->CurlObject,CURLOPT_HTTPHEADER,$array);
	}

	public function set_connectonly($value){
		$this->setopt($this->CurlObject,CURLOPT_CONNECT_ONLY,$value);
	}

	public function set_nobody($value){
		$this->setopt($this->CurlObject,CURLOPT_NOBODY,$value);
	}

	public function set_returntransfer($value){
		$this->setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,$value);		
	}

	public function set_customrequest($value){
		$this->setopt($this->CurlObject,CURLOPT_CUSTOMREQUEST,$value);
	}

	public function set_transfertext($value){
		$this->setopt($this->CurlObject,CURLOPT_TRANSFERTEXT,$value);		
	}
	
	public function set_autoreferer($value){
		$this->setopt($this->CurlObject,CURLOPT_AUTOREFERER,$value);
	}

	public function set_binarytransfer($value){
		$this->setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,$value);
	}

	public function set_connecttimeout($value){
		$this->setopt($this->CurlObject,CURLOPT_CONNECTTIMEOUT,$value);
	}

	public function set_httpproxytunnel($value){
		$this->setopt($this->CurlObject,CURLOPT_HTTPPROXYTUNNEL,$value);
	}

	public function set_proxy($value){

        $this->setopt($this->CurlObject, CURLOPT_PROXY, $value);
        $this->setopt($this->CurlObject, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        
	}
	public function execute(){
	   $this->CurrentOutput=curl_exec($this->CurlObject);
	   // var_dump($this->CurrentOutput);
	   return $this->CurrentOutput;
	}

	public function telnet(){
		$this->setopt($this->CurlObject,CURLOPT_PROTOCOLS,CURLPROTO_TELNET);
	}

	public function reset(){
		return $this->CurrentOutput=curl_reset($this->CurlObject);
	}

	public function post_fields($fields){
		$fields_string='';
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        // $result=rtrim($fields_string, '&');
        $this->setopt($this->CurlObject,CURLOPT_POST,count($fields));
        // var_dump($fields);
        // echo count($fields);
        $this->setopt($this->CurlObject,CURLOPT_POSTFIELDS, $fields_string);
        // return $result;
        // var_dump($fields_string);
	}

	public function Get($url){
		if(!$this->Method)
		{
			$this->Method="Get";
		}
		$this->init();
		$this->set_url($url);
	    $this->execute();
		return $this->CurrentOutput;	
	}

	public function forward($url,array $get=[],array $post=[]){
		if(empty($get)){
			$this->async_post($url,$post);
		}else{
			$url=$url.http_build_query($get);
			$this->async_get($url);
		}
	}

	public function async_get($url){
		
		$this->CurlObject=curl_init();
	    curl_setopt($this->CurlObject, CURLOPT_URL, $url);
	    curl_setopt($this->CurlObject,CURLOPT_CONNECTTIMEOUT,1);
		curl_setopt($this->CurlObject,CURLOPT_HEADER,false);
		curl_setopt($this->CurlObject,CURLOPT_NOBODY,true);
	    curl_setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,0);
	    $response=curl_exec($this->CurlObject);
		curl_close($this->CurlObject);
		return $response;	
	}

	public function async_post($url,$fields){
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
		$this->CurlObject=curl_init();
	    curl_setopt($this->CurlObject, CURLOPT_URL, $url);
	    curl_setopt($this->CurlObject,CURLOPT_POST, count($fields));
        curl_setopt($this->CurlObject,CURLOPT_POSTFIELDS, $fields_string);
	    curl_setopt($this->CurlObject,CURLOPT_CONNECTTIMEOUT,1);
		curl_setopt($this->CurlObject,CURLOPT_HEADER,false);
		curl_setopt($this->CurlObject,CURLOPT_NOBODY,true);
	    curl_setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,0);
	    $response=curl_exec($this->CurlObject);
		curl_close($this->CurlObject);
		return $response;	
	}

	public function curl_post($url,$fields){

		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        //open connection
        $this->CurlObject = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($this->CurlObject,CURLOPT_URL, $url);
        curl_setopt($this->CurlObject,CURLOPT_POST, count($fields));
        curl_setopt($this->CurlObject,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($this->CurlObject,CURLOPT_RETURNTRANSFER,1);
        //execute post
        $response=curl_exec($this->CurlObject);
        //close connection
        curl_close($this->CurlObject);
		return $response;
	}

	public function pause_all(){
		curl_pause($this->CurlObject,'CURLPAUSE_ALL');
		return 1;
	}

	public function unpause(){
		curl_pause($this->CurlObject,'CURLPAUSE_CONT');
		return 1;
	}

	public function request($url) {

		$cmd = "curl ".$url." > /dev/null 2>/dev/null &";

		// if (!$this->CurlDebug) {
		// $cmd .= " > /dev/null 2>/dev/null &";
		// }

		exec($cmd, $output, $exit);
		
		return $exit == 0;
	}

}

?>