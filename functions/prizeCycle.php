<?php
include($_SERVER["DOCUMENT_ROOT"]."/functions/settings.php");

$default_prize = 100;

if($_SERVER['REMOTE_ADDR'] == "205.186.175.57")
{
	$server = 1;
}

//GET AVERAGE OF REFERRALS
$query = mysql_query("SELECT * FROM users WHERE referrals > 0")
or die(mysql_error());
while($row = mysql_fetch_array($query))
{
	$num += $row["referrals"];
}
$entryLimit = ceil($num/mysql_num_rows($query));

/* Select users who:
    - Have a UUID
    - Checked in during the past 30 days
    - Haven't claimed in the past 7 days
*/
$query = mysql_query("SELECT * FROM users WHERE uuid != '' AND last_checkin > ".(time()-(3600*24*30))." AND (last_claim IS NULL || last_claim < ".(time()-(3600*24*7)).")")
or die(mysql_error());
while($row = mysql_fetch_array($query))
{
  //Limit amount of entries to average referrals not 0
	for($i=0;$i<$row["referrals"]+1;$i++)
	{
		if($i < $entryLimit)
		{
			$users[] = $row["username"];
			$last_claims[] = $row["last_claim"];
		}
	}
}

//Choose winner
$winningNumber = mt_rand(0,count($users)-1);
$winningUsername = $users[$winningNumber];
$winningLastClaim = $last_claims[$winningNumber];

//Figure out prize amount
$query = mysql_query("SELECT * FROM results ORDER BY id DESC LIMIT 1")
or die(mysql_error());
if(mysql_num_rows($query))
{
	$row = mysql_fetch_array($query);
	if(!$row["claimed"])
	{
		$prize = $default_prize+$row["prize"];
	}
	else
	{
		$prize = $default_prize;
	}
}
else
{
	$prize = $default_prize;
}

//Get today's message
$file = "notificationMessages.txt";
$handle = fopen($file, "a+");
$contents = fread($handle, filesize($file));
fclose($handle);
$explodedContents = explode("\n",$contents);
if($explodedContents[0] < count($explodedContents)-1)
{
	$explodedContents[0]++;
}
else
{
	$explodedContents[0] = 1;
}
$message = $explodedContents[$explodedContents[0]];
$handle = fopen($file, "w+");
fwrite($handle, implode("\n",$explodedContents));
fclose($handle);

//Check bonus amount
$query = mysql_query("SELECT * FROM checkins WHERE username='".mysql_escape_string($winningUsername)."' AND unix > '".mysql_escape_string($winningLastClaim)."'") or die(mysql_error());
$bonus = mysql_num_rows($query);

echo "<pre>";
print_r($users);

echo "('".$winningUsername."', '".$prize."', '".$bonus."', ".time().")";

if($server) 
{
  mysql_query("INSERT INTO results (username, prize, bonus, unix) VALUES ('".$winningUsername."', '".$prize."', '".$bonus."', ".time().")");
  send2SL($notificationChannel, str_replace("%1", "L$".$prize, $message), 0);
  mail("johnathancroom@gmail.com","New Winner Set",$winningUsername." L$".$prize,"From:noreply@freelindenlottery.com");
}