<?php

/*
 * set var
 */

$cfHost = "localhost";
$cfUser = "root";
$cfPassword = "1234";
$cfDatabase = "fai_fac";


// A$192dijd

/*
 * connection mysql
 */

$meConnect = mysql_connect($cfHost, $cfUser, $cfPassword) or die("Error conncetion mysql...");
$meDatabase = mysql_select_db($cfDatabase);
mysql_query("SET NAMES UTF8");
mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci';");
set_time_limit(0);
ini_set('mysql.connect_timeout','0');
ini_set('max_execution_time', '0');
?>
