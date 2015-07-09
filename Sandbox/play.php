<?php
//Registration test.
//Dblue allows unix terminal command line style interaction with mysql database. (ls,cd,mv,etc) 
$Service->DBlue()->cd('ProtePeople');

$lss=$Service->DBlue()->ls();

foreach($lss as $ls){
	var_dump($ls);
	echo "<br><br>";
}



 ?>
