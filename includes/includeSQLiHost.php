<?php 
//Connnect to mySQL database
$mysqli = new mysqli('localhost', 'amomsdb', 'Amer!canGene515', $moduleDB);
$moduleAdministrator = 'Jay Forrest';
$moduleAdminEmail = 'jay.forrest@library.gatech.edu';
//Connect or notify administrator and user of connection failure
if ($mysqli->connect_error) {
  $body = "Connecting to the $moduleName failed " . $mysqli->connect_errno . "   " . $mysqli->connect_error ;
  mail($moduleAdminEmail, $moduleName.' Connection error', $body);
  die('failed to connect to database, email notification has been sent to System Administator: ' . $moduleAdministrator . '');
}
?>
