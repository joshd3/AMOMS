<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
$languagePref = $_SESSION['languagePref'];
  $query = "SELECT `provider`  FROM `".$moduleDB."`.`user` WHERE `isActive` = 1 AND `dateAcctDeleted` IS NULL AND `userId` like '".$_SESSION['amomsId']."'"; 
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
  //Assign roles
  if ($row[provider] == 1) { $accessProvider = 1; $accessCheck++; } // If provider role assigned, enable provider access
  // close result set
  $result->close();
//Clean Text inputs
  $cleanPatient = htmlentities($_POST['patient'], ENT_COMPAT, 'UTF-8');
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlPatient = $mysqli->real_escape_string($cleanPatient);
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
if ($_POST['value'] == 'end') {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientprescription` SET `dateEnded` =  curDate()  WHERE `id` = '".$mysqlRowId."' LIMIT 1");
  if ($languagePref == 'spa') { 
    echo "La prescripción ".$mysqlRowId." terminó";
  } else {
    echo "Prescription ".$mysqlRowId." ended";
  }
} elseif ($_POST['value'] == 'true') {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientprescription` SET `".$mysqlField."` =  1  WHERE `id` = '".$mysqlRowId."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  echo "Genérico aceptable para el número de la prescripción ".$mysqlRowId."<br />\n";
	} else {	
	  echo "Generic okay for prescription number".$mysqlRowId."<br />\n";
	}
  }
} elseif ($_POST['value'] == 'false') {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientprescription` SET `".$mysqlField."` =  0  WHERE `id` = '".$mysqlRowId."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  echo "Genérico no permitido para el número de receta ".$mysqlRowId."<br />\n";
	} else {	
	  echo "Generic not allowed for prescription number ".$mysqlRowId."<br />\n";
	}
  }
} else { //textbox 
  $mysqli->query("UPDATE `".$moduleDB."`.`patientprescription` SET `".$mysqlField."` =  '".$mysqlValue."'  WHERE `id` = '".$mysqlRowId."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  if ($mysqlField == 'medication') {$mysqlField == 'medicación';}
	  if ($mysqlField == 'dosage') {$mysqlField == 'dosificación';}
	  if ($mysqlField == 'frequency') {$mysqlField == 'frecuencia';}
	  if ($mysqlField == 'notes') {$mysqlField == 'notas';}
	  echo "Prescripción ".$mysqlRowId." ".$mysqlField." actualizada.<br />\n";
	} else {	
	  echo "Prescription ".$mysqlRowId." ".$mysqlField." updated.<br />\n";
	}
  }
}
?>
