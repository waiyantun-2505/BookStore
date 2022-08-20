<?php
$host="localhost";
$user="root";
$pass="";
$database="booklet";
$connection=mysql_connect($host,$user,$pass)
or die ("Couldn't connect to database");
mysql_select_db($database);
?>
