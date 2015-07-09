<?php
namespace index;
require_once(__DIR__."/Engine/Prote/Zed.php");
if($Service->Config()->debug_mode_is_on()){
	$Service->enable_route('Main','System');
}else{
	$Service->enable_route('Main');
}
$Service->Router()->run();

 
?>