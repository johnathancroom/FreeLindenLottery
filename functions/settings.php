<?php
include($_SERVER["DOCUMENT_ROOT"]."/functions/keys.php");

date_default_timezone_set("America/Phoenix");
mysql_connect($_ENV["db_host"], $_ENV["db_user"], $_ENV["db_pass"]) or die(mysql_error());
mysql_select_db($_ENV["db_table"]) or die(mysql_error());

include($_SERVER["DOCUMENT_ROOT"]."/functions/send2SL.php");
include($_SERVER["DOCUMENT_ROOT"]."/lib/simple_html_dom.php");