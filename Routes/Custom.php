<?php
/**
 * @package Routes
 * @Caution Heavy use of closures Ahead
 **/

//SET ERROR PAGES AND HEADERS BEFORE RUNNING THE ROUTER
$Router->before('GET|POST', '/.*', function() {
	header('X-Powered-By: TheZedEngine|Prote');
	header('Server: Kemosabe 1.0');

});

//Custom 404
$Router->set404(function() {
	header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	echo '404, Page not Found!';
});


?>