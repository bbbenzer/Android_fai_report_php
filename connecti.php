<?php
define('DB_USER', "root"); 				// db user
define('DB_PASSWORD', "");
// define('DB_PASSWORD', "A$192dijd"); 	// db password (mention your db password here)
define('DB_DATABASE', "fai_fai"); 			// database name
define('DB_SERVER', "localhost"); 		// db server

$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
// Connection error
if(!$mysqli){
  die('Error : ' . $mysqli->connect_error);
  echo "0@"."0@"."x@";
}
$strCountryCode = "US";
mysqli_query($mysqli, "SET NAMES UTF8");
 ?>
