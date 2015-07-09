<?php
/**
 * @package Routes
 * @Caution Heavy use of closures Ahead
 **/
 
/**
 * @route www.vtuacademy.com/Status/
 **/

$Router->get('/Status/', function() use ($Service) {
	if($Service->Config()->debug_mode_is_on()){

		echo "Monkey Do.";
	}
	else
	{
		echo "All Systems Are Go!";
	}
});


$Router->get('/hackers.txt',function() use ($Service) {
	$x= <<<EOF
	"Don't think you are. Know you are."<br>
The creators of this website believe that hackers are the ones who 
have challenged the system, the status quo time and again.
They are the ones who have moved the world into the age of technology.


This is a special place where hackers are not permitted to make changes ;)
So, if you have hacked into our website.You have earned yourself some respect
and we would like to acknowledge that.

Append your name, date, location,
contact and info as you choose.

**Hackers Wall of fame**

1.	Name: Zed.  
	Contact: ZedM1010@gmail.com

2.	Name: 
	Contact:
	Vuln: 
EOF;
echo nl2br($x);
});

$Router->get('/install/x54/', function() use ($Service) {
		// $auth=$Service->Auth();
		// $auth->install();
	////////////////////////////////////////////////////////
	/////INSTALLATION STARTS HERE//////////////////////////
		// $Service->DbSession()->install();
		// $Service->Prote()->DBI()->People()->install();
		// $Service->VAC()->Habba()->DBI()->AplParticipantMap()->install();
		// $Service->VAC()->Habba()->DBI()->VolunteerMap()->install();
		// $Service->VAC()->College()->install();

		//VERSION CONTROLLING HERE ON.
		// $Service->Prote()->DBI()->People()->ResetMap()->install();
});

$Router->get('/update/x/', function() use ($Service) {       
	
	if($Service->Auth()->logged_in() && $Service->Prote()->DBI()->People()->GroupMap()->exists($Service->Auth()->get_uid(),4)){
			
			// $payloads=(
				// array("ALTER TABLE `ProteCommunity` ADD `Background` VARCHAR(255) NOT NULL ;"
				// ));

		// $payloads=(
		// 		array(
		// 			"ALTER TABLE `ProteCommunityEventSingles` CHANGE `PaymentRecievedAt` `RegisteredAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;"
		// 			,"ALTER TABLE `ProteCommunityEventSingles` CHANGE `RegisteredAt` `RegisteredAt` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;"
		// 			,"ALTER TABLE `ProteCommunityEventTeam` CHANGE `PaymentRecievedAt` `RegisteredAt` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;"
		// 			)
		// 		);

		$payloads=(
				array("ALTER TABLE `ProtePeopleGroupMap` ADD `Id` INT(255) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`Id`) ;"
					,"ALTER IGNORE TABLE  `habba.dev`.`ProtePeopleGroupMap` ADD UNIQUE `groupmapunique` (`Uid`, `GroupId`) COMMENT '';"
				));

		

		
	    	$this->Database()->drop_payload($payloads,$this);
}
});

$Router->get('/backup/db/[0-9]+', function($d) use ($Service) {       
	
	if($Service->Auth()->logged_in() && $Service->Prote()->DBI()->People()->GroupMap()->exists($Service->Auth()->get_uid(),4)){
			
$Service->Database()->backup($d);		
}
});
		// $auth=$Service->Auth();
		// $auth->install();
	////////////////////////////////////////////////////////
	/////INSTALLATION STARTS HERE//////////////////////////
		// $Service->DbSession()->install();
		// $Service->Prote()->DBI()->People()->install();
		// $Service->VAC()->Habba()->DBI()->AplParticipantMap()->install();
		// $Service->VAC()->Habba()->DBI()->VolunteerMap()->install();
		// $Service->VAC()->College()->install();

		//VERSION CONTROLLING HERE ON.
		// $Service->Prote()->DBI()->People()->ResetMap()->install();
		// $Service->VAC()->Habba()->DBI()->AplParticipantMap()->update();


