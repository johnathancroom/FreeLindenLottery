<?php
include($_SERVER["DOCUMENT_ROOT"]."/functions/settings.php");

$num = 1;

if(date("j") % 2 == 0) 
{//even number
	$query = mysql_query("SELECT * FROM users WHERE uuid != '' AND im_notify != '0'")
	or die(mysql_error());
	while($row = mysql_fetch_array($query))
	{
		$list[$num] = "|".$row["uuid"];
		$num++;
	}
}
else
{
  $query = mysql_query("SELECT * FROM users WHERE uuid != '' AND im_notify = '1'")
  or die(mysql_error());
  while($row = mysql_fetch_array($query))
  {
  	$list[$num] = "|".$row["uuid"];
  	$num++;
  }
}

$page = $_GET["page"];
$limit = 52;

if($limit*($page+1) < count($list))
{
	echo count($list);
}
else
{
	echo "done";
}
for($i=$page*$limit;$i<$limit*($page+1);$i++)
{
	if(isset($list[$i]))
	{
		echo $list[$i];
	}
}