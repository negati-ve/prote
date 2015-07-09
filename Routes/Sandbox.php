<?php

$Router->get('/play',function() use ($Service) {
	//if($Service->Auth()->logged_in()){
//echo "No momos for you bro.";
	//}else{
	
		include($Service->Config()->get_basepath()."/Sandbox/play.php");
	
});
