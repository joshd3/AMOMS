<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
$languagePref = $_SESSION['languagePref'];
  $query = "SELECT `receptionist` ,  `officeMgr` FROM `".$moduleDB."`.`user` WHERE `isActive` = 1 AND `dateAcctDeleted` IS NULL AND `userId` like '".$_SESSION['amomsId']."'"; 
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
  //Assign roles
  if ($row[receptionist] == 1) { $accessReceptionist = 1; $accessCheck++; } // If receptionist role assigned, enable receptionist access
  if ($row[officeMgr] == 1) { $accessOfficeMgr = 1; $accessCheck++; } // If Office Manager role assigned, enable Office Manager access
  // close result set
  $result->close();
//Clean Text inputs
  $cleanPatient = htmlentities($_POST['patient'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlPatient = $mysqli->real_escape_string($cleanPatient);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
  //If receptionist or Office Manager Role Write Update to Table
  if ($accessReceptionist == 1 || $accessOfficeMgr == 1) {
    $mysqli->query("UPDATE `".$moduleDB."`.`patient`SET `".$mysqlField."` = '".$mysqlValue."' WHERE `patientId` = '".$mysqlPatient."' LIMIT 1");
    if ($mysqli->affected_rows >= 1) {
      if ($languagePref == 'spa') { echo $mysqlField." para ".$mysqlUserName." cambiando a ".$mysqlValue."<br />\n";  } else { echo $mysqlField." for ".$mysqlUserName." changed to ".$mysqlValue."<br />\n"; }
    } else {
        if ($languagePref == 'spa') { 
          echo "Error de base de datos".$mysqlField." para".$mysqlUserName." NO cambiado a ".$mysqlValue." Vuelve a cargar la página e inténtalo de nuevo.<br />\n"; 
        } else { 
          echo "DB Error".$mysqlField." for ".$mysqlUserName." NOT changed to ".$mysqlValue." Please reload the page and try again.<br />\n"; 
        }
    }
  } else {
       if ($languagePref == 'spa') { echo "Usted no tiene permiso para hacer cambios información de contacto del paciente.<br />\n";  } else { echo "You do not have permission to make changes patient contact Information.<br />\n"; }
  }
$mysqli->close();
?>
