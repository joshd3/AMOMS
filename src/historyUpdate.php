<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
$languagePref = $_SESSION['languagePref'];
  $query = "SELECT `provider`  FROM `".$moduleDB."`.`user` WHERE `isActive` = 1 AND `dateAcctDeleted` IS NULL AND `userId` like '".$_SESSION['amomsId']."'"; 
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
  //Assign roles
  if ($row[provider] == 1) { $accessProvider = 1; $accessCheck++; } // If receptionist role assigned, enable receptionist access
  // close result set
  $result->close();
//Clean Text inputs
  $cleanPatient = htmlentities($_POST['patient'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlPatient = $mysqli->real_escape_string($cleanPatient);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
  if ($_POST['key'] == 1 ) { $relationEnglish = 'self';  $relationSpanish = 'yo'; $key = 1; } 
  if ($_POST['key'] == 10 ) { $relationEnglish = 'mother'; $relationSpanish = 'madre'; $key = 10; } 
  if ($_POST['key'] == 100 ) { $relationEnglish = 'father'; $relationSpanish = 'padre'; $key = 100; } 
  if ($_POST['key'] == 1000 ) { $relationEnglish = 'sibling'; $relationSpanish = 'hermano'; $key = 1000; } 
  if ($_POST['key'] == 10000 ) { $relationEnglish = 'child'; $relationSpanish = 'niño'; $key = 10000; } 
if ($_POST['value'] == 'true') {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientmedicalhistory` SET `".$mysqlField."` =  `".$mysqlField."` + ".$key."  WHERE `patientId` = '".$mysqlPatient."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  echo "Historial del paciente actualizado a true para".$mysqlField.": $relationSpanish<br />\n";
	} else {	
	  echo "Patient History Updated to true for ".$mysqlField.": $relationEnglish<br />\n";
	}
  }
} elseif ($_POST['value'] == 'false') {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientmedicalhistory` SET `".$mysqlField."` =  `".$mysqlField."` - ".$key."  WHERE `patientId` = '".$mysqlPatient."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  echo "Historial del paciente actualizado a falso para".$mysqlField.": $relationSpanish<br />\n";
	} else {	
	  echo "Patient History Updated to false for ".$mysqlField.": $relationEnglish<br />\n";
	}
  }
} else { //textbox 
  $mysqli->query("UPDATE `".$moduleDB."`.`patientmedicalhistory` SET `".$mysqlField."` =  '".$mysqlValue."'  WHERE `patientId` = '".$mysqlPatient."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if ($languagePref == 'spa') {
	  echo "Lista de alergias para el paciente actualizada.<br />\n";
	} else {	
	  echo "Patient Allergy List Updated.<br />\n";
	}
  }
}
?>
