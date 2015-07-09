<?php
namespace Html;

class Html{

	public $Error=NULL;
	public $auth_token=NUll;

	public function __construct(\DIC\Service $Service){
		$this->Service=$Service;
	}

	// public function html_decode
	public function post_var(){
		if(isset($POST[$var]))
			if(!empty($POST[$var]))
				return $POST[$var];
	}

	public function clean_url($str) {
		
    	$accent = array(' ','ű','á','é','ú','ő','ó','ü','ö','í','Ű','Á','É','Ú','Ő','Ó','Ü','Ö','Í','.');
	    $clean = array('-','u','a','e','u','o','o','u','o','i','U','A','E','U','O','O','U','O','I','');
	    $str = str_replace($accent, $clean, $str);
	    return preg_replace('/[^A-Za-z0-9-]/', '', $str);
	}

	public function secure_post_text($text){
		if(get_magic_quotes_gpc()){
			  $text = stripslashes($text);
			}
		$text = htmlentities($text);
		return $text;
	} 

}