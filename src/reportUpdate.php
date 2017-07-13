<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
$AMOMSReceptionEmail = 'jay.forrest@gatech.edu';
 
//Clean Text inputs
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanApptTime = htmlentities($_POST['apptTime'], ENT_COMPAT, 'UTF-8');
  $cleanApptDate = htmlentities($_POST['apptDate'], ENT_COMPAT, 'UTF-8');
  $cleanApptProvider = htmlentities($_POST['apptProvider'], ENT_COMPAT, 'UTF-8');
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlApptTime = $mysqli->real_escape_string($cleanApptTime);
  $mysqlApptDate = $mysqli->real_escape_string($cleanApptDate);
  $mysqlApptProvider = $mysqli->real_escape_string($cleanApptProvider);
  if ($_POST[field] == 'apptReminderPhone') {
	//Phone call made - update patient table to log contact
	$query = "UPDATE `".$moduleDB."`.`patient` SET `lastContactAttempt` = CURDATE() , `contactNote` = CONCAT(`contactNote`, CURDATE(), ' ".$fieldNames[apptPhoneReminder]."\n') 
	WHERE `patientId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlRowId." ".$fieldNames[apptPhoneReminder];
	} else {
		echo $fieldNames[updateFailed];
	} 
  }
  if ($_POST[apptProvider]) {
	//Select Patient Information
	$query = "SELECT `nameLast`, `nameFirst`, `namePreferred`, `email` FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$mysqlRowId."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $patientNameLast = $row->nameLast;
    $patientNameFirst = $row->nameFirst;	
    $patientNamePreferred = $row->namePreferred;
	$patientEmail = $row->email;	  
	$result->close;	
	//Select Patient Information
	$query = "SELECT `nameLast` , `nameFirst` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$mysqlApptProvider."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $providerNameLast = $row->nameLast;
    $providerNameFirst = $row->nameFirst;	  
	$result->close;	
	  
	//Generate Email
	if (strlen($patientNamePreferred) == 0 ) {	
	  $body = $patientNameLast." ".$patientNameFirst."\r\n\r\n";
	} else {
	  $body = $patientNameLast." ".$patientNamePreferred."\r\n\r\n";
	}
	$body .= $fieldNames[apptEmailReminderBody]."\r\n".$mysqlApptDate." ".$mysqlApptTime." (".$providerNameFirst." ".$providerNameLast.")\r\n".$fieldNames[apptEmailReminderBody2];
	$header = "From: ".$AMOMSReceptionEmail;
	mail($patientEmail, $fieldNames[apptEmailReminderSubject], wordwrap($body,120) , $header);
	  
	//Email sent made - update patient table to log contact
    $query = "UPDATE `".$moduleDB."`.`patient` SET `lastContactAttempt` = CURDATE() , `contactNote` = CONCAT(`contactNote`, CURDATE(), ' ".$fieldNames[apptEmailReminder]."\n') 
	WHERE `patientId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlRowId." ".$fieldNames[apptEmailReminder];
	} else {
		echo $fieldNames[updateFailed];
	} 	
  }
