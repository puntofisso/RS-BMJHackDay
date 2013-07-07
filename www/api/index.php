<?php
require('Toro.php');

/* Generic Functions */

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

/* API Endpoint Handlers */

class RootHandler {
    function get() {
      header('Access-Control-Allow-Origin: *');
      echo "BMJHack<br/>";
    }
}

class FsoHandler {
    function get() {
      header('Access-Control-Allow-Origin: *');
      echo "Figuring Shit Out<br/>";
      echo "I do very little, but a lot of it<br/>";
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
		header('Access-Control-Allow-Origin: *');
		echo json_encode($out);
    }
}

class UserStatsHandler {
    function get($username) {
     	$query = "SELECT * from users where username='$username';";
      	$result = mySQLquery($query);
      	$out = array();
      	while($row = mysql_fetch_assoc( $result )) {
			$out['username'] = $row['username'];
			$out['fullname'] = $row['fullname'];
			$out['points'] = $row['points'];
			$out['percentcorrect'] = $row['percentcorrect'];
			$out['level'] = $row['level'];
		}
		header('Access-Control-Allow-Origin: *');
		echo json_encode($out);
    }
}

class QuestionsHandler {
    function get($catname,$username) {

    	$query = "";
     	if ($catname == "all") {
     		$query = "SELECT q.* from questions q where q.qid not in (SELECT questionid from answeredquestions WHERE username='$username') order by q.AVScore asc;";
  		} else {
  			$query = "SELECT q.* from questions q where q.FriendlyName='$catname' and q.qid not in (SELECT questionid from answeredquestions WHERE username='$username') order by q.AVScore asc;";
  		}
  		$result = mySQLquery($query);
  		$out = array();


  			while($row = mysql_fetch_assoc( $result )) {
  				$thisout=array();
		 		$thisout['qid'] = $row['QID'];
		 		$thisout['category'] = $row['FriendlyName'];
		 		$xml = $row['XML'];

		 		// Extract XML
		 		$dom = new DOMDocument;
				$dom->loadXML($xml);

				$scoreIsPresent = FALSE;
				// question
				$qText = $dom->getElementsByTagName('qTxt');
				$string = "";	
				foreach ($qText as $question) {
    				foreach($question->childNodes as $child) {
        				if ($child->nodeType == XML_CDATA_SECTION_NODE) {
            				$string = $string . $child->textContent;
        				}
    				}
				}
				$thisout['question'] = strip_tags($string);

				// answers
				$opts = $dom->getElementsByTagName('o');
				$optsArray = array();
				foreach ($opts as $opt) {
					$thisScore = array();
					$thisScore['score'] = $opt->getAttribute('score');
					
					if ($thisScore['score'] <> "")
						$scoreIsPresent = TRUE;

					$optiontext = $opt->getElementsByTagName('oTxt');
					$optionstring = "";
					foreach ($optiontext as $text) {
    					foreach($text->childNodes as $child) {
        					if ($child->nodeType == XML_CDATA_SECTION_NODE) {
            					$optionstring = $optionstring . $child->textContent;
        					}
    					}
					}
					$thisScore['option'] = strip_tags($optionstring);
					$optsArray[] = $thisScore;
				}

				$thisout['answers'] = $optsArray;

		 		//$thisout['xml'] = $row['XML'];
		 		if (!$scoreIsPresent)
		 			$thisout['flag'] = 'noscore';
		 		else
		 			$thisout['flag'] = 'score';

		 		$out[] = $thisout;
			}
			header('Access-Control-Allow-Origin: *');
	    	echo json_encode($out);
	    
    }
}


class CategoriesHandler {
    function get($count) {
    	if ($count == "all") {
    		$query = "SELECT FriendlyName, count(FriendlyName) c from questions group by FriendlyName";
    	} else {
    		$query = "SELECT FriendlyName, count(FriendlyName) c from questions group by FriendlyName having c>$count;";
    	}
     	
      	$result = mySQLquery($query);
      	$out = array();
      	while($row = mysql_fetch_assoc( $result )) {
      		$thisout = array();
			$thisout['category'] = $row['FriendlyName'];
			$thisout['count'] = $row['c'];
			$out[] = $thisout;
		}
		header('Access-Control-Allow-Origin: *');
		echo json_encode($out);
    }
}

class LeaderboardHandler {
    function get($count) {
    	$query = "SELECT points, username, fullname, level from users order by points desc";
     	
      	$result = mySQLquery($query);
      	$out = array();
      	$i = 0;
      	while($row = mysql_fetch_assoc( $result )) {
      		$i++;
      		$thisout = array();
			$thisout['position'] = $i;
			$thisout['points'] = $row['points'];
			$thisout['level'] = $row['level'];
			$thisout['username'] = $row['username'];
			$thisout['fullname'] = $row['fullname'];
			$out[] = $thisout;
		}
		header('Access-Control-Allow-Origin: *');
		echo json_encode($out);
    }
}

/* Toro main list of endpoints */

Toro::serve(array(
    "/" => "RootHandler",
    "/fso" => "FsoHandler",
    "/test" => "TestHandler",
    "/userstats/:string" => "UserStatsHandler",
    "/questions/:string/:string" => "QuestionsHandler",
    "/categories/:alpha" => "CategoriesHandler",
    "/leaderboard" => "LeaderboardHandler",
));

?>
