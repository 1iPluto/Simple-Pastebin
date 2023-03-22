<?php 

require "config.php";

$GLOBALS['conn'] = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
header("X-Powered-By: ripper.lol");

require "functions.php";
