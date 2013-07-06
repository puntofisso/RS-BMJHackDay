<?php
require('Toro.php');

function mySQLquery($query) {
	$DB_HOST = "localhost";
	$DB_USER = "ren";
	$DB_PASS = "ren";
	$DB_NAME = "bmj";
	mysql_connect($DB_HOST, $DB_USER, $DB_PASS) or die(mysql_error()." | HOST ".$DB_HOST." USER ".$DB_USER." PASS ".$DB_PASS ." NAME ".$DB_NAME." | Error on connect ");
	mysql_select_db($DB_NAME) or die(mysql_error()." | HOST ".$DB_HOST." USER ".$DB_USER." PASS ".$DB_PASS ." NAME ".$DB_NAME." | Error on select db ");

	$result = mysql_query("$query") or die(mysql_error()." | HOST ".$DB_HOST." USER ".$DB_USER." PASS ".$DB_PASS ." NAME ".$DB_NAME. " | Error on query " );  

	// keeps getting the next row until there are no more to get
	#while($row = mysql_fetch_array( $result )) {
	#	// Print out the contents of each row into a table
	#}
	return $result; 
}

class RootHandler {
    function get() {
      echo "BMJHack\n";
    }
}

class FsoHandler {
    function get() {
      echo "Figuring Shit Out\n";
    }
}

class TestHandler {
    function get() {
     	$query = "SELECT * from questions;";
      	$result = mySQLquery($query);
      	$out = array();
      	while($row = mysql_fetch_assoc( $result )) {
			$out[] = $row['QID'];
		}
		echo json_encode($out);
    }
}

Toro::serve(array(
    "/" => "RootHandler",
    "/fso" => "FsoHandler",
    "/test" => "TestHandler",
));

?>
