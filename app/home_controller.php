<?php
include($_SERVER["DOCUMENT_ROOT"]."/functions/settings.php");

include($_SERVER["DOCUMENT_ROOT"]."/app/home_helpers.php"); /* Include helper functions */

/*LOGIN*/
if(isset($_POST["submitUsername"]))
{
  $submitUsername = str_replace(" ", ".", $_POST["submitUsername"]);
  
  /* Username doesn't exist */
  if($submitUsername == "" || !curl("https://my.secondlife.com/".str_replace(" ", ".", $submitUsername)))
  {
    $_flash["error"][] = "The username you entered is invalid";
  }
  else
  {
    $query = mysql_query("SELECT * FROM users WHERE username='".mysql_escape_string(str_replace(" ", ".", $submitUsername))."'") or die(mysql_error());
    if(!mysql_num_rows($query))
    {
      /* Get UUID */
      /*$html = explode(",", curl("http://kubwa.net/_slworld/name2key.php?name=".$submitUsername));
      if($html[1] == "NOT_FOUND") // UUID not found
      {
        $uuid = "";
        mail("johnathancroom@gmail.com", "UUID not in kubwa.net database", "The UUID for ".str_replace(" ", ".", $submitUsername)." was not found in  the database. Manual insertion needed.", "From:noreply@freelindenlottery.com");
      }
      else
      {
        $uuid = $html[1];
        $submitUsername = $html[2];
        $new = explode(" ", $submitUsername);
        if($new[1] == "Resident") $submitUsername = $new[0];
      }*/

      $uuid = "";
      
      if($_COOKIE["referral"] != "")
      {
        $query = mysql_query("SELECT * FROM users WHERE id='".mysql_escape_string($_COOKIE["referral"])."'") or die(mysql_error());
        $row = mysql_fetch_array($query);
        $newReferrals = $row["referrals"]+1;
        $referralUsername = $row["username"];
        mysql_query("UPDATE users SET referrals='".mysql_escape_string($newReferrals)."' WHERE id='".mysql_escape_string($referral)."'");
      }

      mysql_query("INSERT INTO users (username, uuid, last_checkin, referee) VALUES ('".mysql_escape_string(str_replace(" ", ".", $submitUsername))."', '".$uuid."', ".time().", '".$referralUsername."')");
      mysql_query("INSERT INTO checkins (username, signup, unix) VALUES ('".mysql_escape_string(str_replace(" ", ".", $submitUsername))."', 1, ".time().")");
    }
    setcookie("username", str_replace(" ", ".", $submitUsername), time()+60*60*24*365);//365 days
    $_flash["notice"][] = "You have been successfully logged in!";
    $recentLogin = str_replace(" ", ".", $submitUsername);
  }
}

/*LOGOUT*/
if(isset($_POST["logout"]))
{
  setcookie("username", "", time()-3600);
  $_flash["notice"][] = "You have been logged out!";
  $recentLogin = "";
}

/* Check if logged in (even recently) */
if(isset($recentLogin))
{
  $username = $recentLogin;
}
else
{
  $username = $_COOKIE["username"];
}

/*SET REFERRAL COOKIE*/
if(isset($_GET["r"]))
{
  setcookie("referral", $_GET["r"], time()+60*60*24*365);//365 days
}

/*SAVE SETTINGS*/
if(isset($_POST["saveSettings"]))
{
  mysql_query("UPDATE users SET im_notify='".mysql_escape_string($_POST["notify"])."' WHERE username='".mysql_escape_string($username)."'") or die(mysql_error());
  $_flash["notice"][] = "Your IM settings have been saved!";
}

/*CHECK WHO YOU ARE + CHECKIN*/
if($username != "")
{
  $query = mysql_query("SELECT * FROM users WHERE username='".mysql_escape_string($username)."'") or die(mysql_error());
  $row = mysql_fetch_array($query);
  $username = $row["username"];
  $userId = $row["id"];
  $last_claim = $row["last_claim"];
  $referrals = $row["referrals"];
  $_im_settings = $row["im_notify"];
  if($row["last_checkin"] < mktime(0, 0, 0, date("n"), date("j"), date("Y")))//mktime = this morning midnight
  {
    mysql_query("INSERT INTO checkins (username, unix) VALUES ('".mysql_escape_string($username)."', ".time().")");
  }
  mysql_query("UPDATE users SET last_checkin='".time()."' WHERE username='".mysql_escape_string($username)."'") or die(mysql_error());
  
  /* Check bonus amount */
  $query = mysql_query("SELECT id FROM checkins WHERE username='".mysql_escape_string($username)."' AND unix > '".mysql_escape_string($last_claim)."'") or die(mysql_error());
	$_bonus = "L$".(mysql_num_rows($query));
}

/*CLAIM*/
if(isset($_POST["claim"]))
{
  $query = mysql_query("SELECT * FROM results ORDER BY id DESC LIMIT 1") or die(mysql_error());
  $row = mysql_fetch_array($query);
  $Wid = $row["id"];
  $Wusername = $row["username"];
  $Wclaimed = $row["claimed"];
  $Wprize = $row["prize"]+$row["bonus"];
  $Wunix = $row["unix"];
  
  $query = mysql_query("SELECT * FROM users WHERE username='".mysql_escape_string($Wusername)."'") or die(mysql_error());
  $row = mysql_fetch_array($query);
  $Wuuid = $row["uuid"];
  
  if($Wusername == $username && !$Wclaimed)
  {
    if(send2SL($payoutChannel, $Wuuid, $Wprize))
    {
      mysql_query("UPDATE results SET claimed='1' WHERE id='$Wid'");
      mysql_query("UPDATE users SET last_claim='".$Wunix."' WHERE username='".mysql_escape_string($Wusername)."'");
      $_flash["notice"][] = "Congratulations! We're sending L$$Wprize to you in-world now.";
    }
    else
    {
      $_flash["error"][] = "Oopsies, something went wrong when claiming your prize. Try again before the day ends.";
    }
  }
}

/*REMOVE QUERY*/
$querystart = explode("?", $_SERVER["REQUEST_URI"]);
if(isset($querystart[1]))
{
  $uri = explode("?", $_SERVER["REQUEST_URI"]);
  header("Location: http://www.freelindenlottery.com".$uri[0]);
  return;
}

/*GET RESULTS*/
$query = mysql_query("SELECT * FROM results ORDER BY id DESC LIMIT 1") or die(mysql_error());
$row = mysql_fetch_array($query);

function getPrettyTime($unix) {
  $time_in_hours = ceil((86400+$unix-time())/3600);
  if($time_in_hours == 1)
  {
    $time_in_minutes = ceil((86400+$unix-time())/60);
    return $time_in_minutes.(($time_in_minutes > 1) ? " minutes" : " minute");
  }
  else
  {
    return $time_in_hours.(($time_in_hours > 1) ? " hours" : " hour");
  }
}

$_results = array(
  "username" => $row["username"],
  "display_username" => str_replace(".", " ", $row["username"]),
  "claimed" => $row["claimed"],
  "prize" => "L$".$row["prize"],
  "bonus" => ($row["bonus"] == "") ? "L$0" : "L$".$row["bonus"],
  "pretty_time" => getPrettyTime($row["unix"])
);

/*TOTAL GIVEN AWAY*/
$query = mysql_query("SELECT prize, bonus FROM results WHERE claimed=1") or die(mysql_error());
$_total_given = 0;
while($row = mysql_fetch_array($query))
{
  $_total_given += $row["prize"]+$row["bonus"];
}
$_total_given = "L$".number_format($_total_given);

/*ADMIN LIST*/
$admins = array(
  "Johnathan.Doolittle",
  "Karissa.Silversmith",
  "Johnny321.Woller"
);

/*JSON DATA FOR GRAPHS*/
/*Format:
  {
  "cols": [
  	{"label":"Date","type":"string"},
  	{"label":"Check-ins","type":"number"},
  	{"label":"Sign-ups","type":"number"}
  		],
  		
  "rows": [
  	{"c":[{"v":"Sep. 12"},{"v":15},{"v":2}]}
  		]
  }
*/
if(in_array($username, $admins))
{
  $array["cols"] = array(
  	array("label" => "Date", "type" => "string"),
  	array("label" => "Check-ins", "type" => "number"),
  	array("label" => "Sign-ups", "type" => "number")
  );
  $query = mysql_query("SELECT * FROM checkins WHERE unix > '".(time()-86400*15)."'");
  while($row = mysql_fetch_array($query))
  {
  	$date[date("M. d", $row["unix"])]["checkins"]++;
  	if($row["signup"])
  	{
  		$date[date("M. d", $row["unix"])]["signups"]++;
  	}
  }
  
  for($i=0;$i<14;$i++)
  {
  	$checkins = $date[date("M. d", mktime(0, 0, 0, date("n"), date("j"), date("Y"))-86400*$i)]["checkins"];
  	if($checkins == null) $checkins = 0;
  	$signups = $date[date("M. d", mktime(0, 0, 0, date("n"), date("j"), date("Y"))-86400*$i)]["signups"];
  	if($signups == null) $signups = 0;
  	$rowsArray[$i] = array("c"=> 
  		array(
  			array("v" => date("M. d",mktime(0, 0, 0, date("n"), date("j"), date("Y"))-86400*$i)), 	
  			array("v" => $checkins),
  			array("v" => $signups)
  		)
  	);
  }
  $array["rows"] = array_reverse($rowsArray);
  
  $_checkin_data = json_encode($array);
}

/*PREVIOUS WINNER */
$query = mysql_query("SELECT * FROM results ORDER BY id DESC LIMIT 1,10") or die(mysql_error());
while($row = mysql_fetch_array($query))
{
  $row["username"] = str_replace(".", " ", $row["username"]);
  $row["prize"] = "L$".$row["prize"];
  $row["bonus"] = ($row["bonus"] == "") ? "L$0" : "L$".$row["bonus"];
  $row["claimed"] = $row["claimed"] ? "Yes" : "No";
  $row["date"] = date("m/d/Y", $row["unix"]);
  $_previous_winners[] = $row;
}

if(isset($_POST["getPreviousWinners"]))
{
  $query = mysql_query("SELECT * FROM results ORDER BY id DESC LIMIT 11,10000") or die(mysql_error());
  while($row = mysql_fetch_array($query))
  {
    $row["username"] = str_replace(".", " ", $row["username"]);
    $row["prize"] = "L$".$row["prize"];
    $row["bonus"] = ($row["bonus"] == "") ? "L$0" : "L$".$row["bonus"];
    $row["claimed"] = $row["claimed"] ? "Yes" : "No";
    $row["date"] = date("m/d/Y", $row["unix"]);
    $all_previous_winners[] = $row;
  }
  echo json_encode($all_previous_winners);
  return;
}

include($_SERVER["DOCUMENT_ROOT"]."/app/home_view.php"); /* Bring in the view */