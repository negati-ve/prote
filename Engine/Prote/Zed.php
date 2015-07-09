<?php
/**
 * @author    TheZed.
 * @version   0.4
 * @new       design changes
 **/

namespace Zed;
use 
Etc\Config,
Etc\AutoLoaders\AutoLoad,
DIC\Service;
date_default_timezone_set("asia/kolkata");
set_include_path(__DIR__);
require_once("Etc/Config.php");
require_once("Etc/AutoLoaders/AutoLoad.php");
$Config=new Config();
$AutoLoad=new AutoLoad($Config);
$Config->set_autoloader($AutoLoad,'standard');

//platform dependence for cloud platforms
//Based on sub domain 
$subdomain=array_shift((explode(".",$_SERVER['HTTP_HOST'])));
$platform='REMOTE';
// session_name("ProteSession");
//Set platform variable
if(getenv('OPENSHIFT_APP_NAME')){
	$platform='OPENSHIFT';
}elseif($subdomain=='dev'){
	$platform="LOCAL";
	$Config->start_debug();
}

switch ($platform) {
	case 'LOCAL':
		//Debug is off. We are on development mode.	
		$Config->set_database_user('root');
		$Config->set_database_pass('ODD');
		$Config->set_database_name('habba.dev');

		break;

	case 'OPENSHIFT':
		//Debug is off. We are on production mode.
		$Config->set_database_host(getenv('OPENSHIFT_MYSQL_DB_HOST'));
		$Config->set_database_port(getenv('OPENSHIFT_MYSQL_DB_PORT'));
		$Config->set_database_user(getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
		$Config->set_database_pass(getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
		$Config->set_database_name(getenv('OPENSHIFT_GEAR_NAME'));
		break;
	default:
		//Debug off. remote unidentified platform
		$Config->set_database_user('ahabba_habba');
		$Config->set_database_pass('thez3dz3pp3lin');
		$Config->set_database_name('ahabba_h4bb4');
		break;
}
// $Config->stop_debug();
//Initiate Service container.	
$Service=new Service($Config);
$Service->Database()->query("SET GLOBAL time_zone = '+5:30';");
?>
