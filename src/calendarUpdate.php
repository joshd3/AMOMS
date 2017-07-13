<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
 
//Clean Text inputs
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $cleanPatient = htmlentities($_POST['patient'], ENT_COMPAT, 'UTF-8');
  $cleanTreatment = htmlentities($_POST['treatment'], ENT_COMPAT, 'UTF-8');
  $cleanProvider = htmlentities($_POST['provider'], ENT_COMPAT, 'UTF-8');
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField); // Hour Value
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
  $mysqlPatient = $mysqli->real_escape_string($cleanPatient);
  $mysqlTreatment = $mysqli->real_escape_string($cleanTreatment);
  $mysqlProvider = $mysqli->real_escape_string($cleanProvider);
if ($_POST['operation'] == 'selectTreatment') {
  $query = "SELECT `duration` FROM `".$moduleDB."`.`treatmentCode` WHERE `treatmentCode` = '".$mysqlValue."'";
  $result = $mysqli->query($query); 
  $row = $result->fetch_object();
  echo $row->duration;
  $result->close;
}
if ($_POST['operation'] == 'schedulePatient') { 
	$calendarTable = 'calendar'.$mysqlProvider; 
	
  //Add row to the table if needed
  $query = "SELECT * FROM `".$moduleDB."`.`".$calendarTable."` WHERE `date` = '".$mysqlRowId."'";
  $result = $mysqli->query($query);
  if ($result->num_rows == 0) {
	$queryInsert = "INSERT INTO `".$moduleDB."`.`".$calendarTable."` (`date`) VALUES ('".$mysqlRowId."')";
	$resultInsert = $mysqli->query($queryInsert);
  }
  $result->close;
  if ($_POST['toggle'] == 'calendarDefault') { //Schedule Appointment
	$query = "UPDATE `".$moduleDB."`.`".$calendarTable."` SET `".$mysqlField."` =  '0".$mysqlPatient."' WHERE `date` = '".$mysqlRowId."'  LIMIT 1";
  } else { //appointmentScheduled that needs to be cancelled
	$query = "UPDATE `".$moduleDB."`.`".$calendarTable."` SET `".$mysqlField."` =  '1".substr($mysqlValue, 8)."' WHERE `date` = '".$mysqlRowId."'  LIMIT 1";
	//emailTrigger to cancel appt
  }
  $mysqli->query($query);
  if ($mysqli->affected_rows >= 1) {
	  echo $fieldNames[calendarUpdate];
  } else {
	  echo $fieldNames[calendarUpdateError];
  }	
	
}
if ($_POST['operation'] == 'masterCalendar' || $_POST['operation'] == 'providerCalendar') {
  if ($_POST['operation'] == 'masterCalendar') { $calendarTable = 'calendarClinic'; }
  if ($_POST['operation'] == 'providerCalendar') { $calendarTable = 'calendar'.$_SESSION['amomsId']; }
  if ($_POST['operation'] == 'schedulePatient') { $calendarTable = 'calendar'.$mysqliProvider; }
	
  //Add row to the table if needed
  $query = "SELECT * FROM `".$moduleDB."`.`".$calendarTable."` WHERE `date` = '".$mysqlRowId."'";
  $result = $mysqli->query($query);
  if ($result->num_rows == 0) {
	$queryInsert = "INSERT INTO `".$moduleDB."`.`".$calendarTable."` (`date`) VALUES ('".$mysqlRowId."')";
	$resultInsert = $mysqli->query($queryInsert);
  }
  $result->close;
  if ($_POST['toggle'] == 'calendarDefault') { //Default Available to Unavailable
    $query = "UPDATE `".$moduleDB."`.`".$calendarTable."` SET `".$mysqlField."` =  '1' WHERE `date` = '".$mysqlRowId."'  LIMIT 1";
  } elseif ($_POST['toggle'] == 'calendarNotAvailable') { // Unavailable to default Available
	$query = "UPDATE `".$moduleDB."`.`".$calendarTable."` SET `".$mysqlField."` =  '2' WHERE `date` = '".$mysqlRowId."'  LIMIT 1";
  } else { //appointmentScheduled that needs to be cancelled
	$query = "UPDATE `".$moduleDB."`.`".$calendarTable."` SET `".$mysqlField."` =  '1".substr($mysqlValue, 8)."' WHERE `date` = '".$mysqlRowId."'  LIMIT 1";
	//emailTrigger to cancel appt
  }
  $mysqli->query($query);
  if ($mysqli->affected_rows >= 1) {
	  echo $fieldNames[calendarUpdate];
  } else {
	  echo $fieldNames[calendarUpdateError];
  }
}
if ($_POST['operation'] == 'base') {
	
  $mysqli->query("UPDATE `".$moduleDB."`.`masterCalendar` SET `".$mysqlField."` =  '".$mysqlValue."' WHERE `providerId` = '".$mysqlRowId."'  LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
	  echo $fieldNames[calendarUpdate];
  } else {
	  echo $fieldNames[calendarUpdateError];
  }
}
