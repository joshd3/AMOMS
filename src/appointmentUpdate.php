<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
 
//Clean Text inputs
  $cleanPatient = htmlentities($_POST['patient'], ENT_COMPAT, 'UTF-8');
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlPatient = $mysqli->real_escape_string($cleanPatient);
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
    if (  $mysqlField == 'treatmentCode' ) { $mysqlTreatmentCode = $mysqlValue; }
  if ($mysqlField == 'followup') {
	 $followupDate = date("Y-m-d", strtotime("+".$mysqlValue." weeks"));
     $mysqli->query("UPDATE `".$moduleDB."`.`patientvisit` SET `callBackDate` =  '".$followupDate."' , `providerId` = '".$_SESSION['amomsId']."' WHERE `appointmentId` = '".$mysqlRowId."' LIMIT 1");
     if ($mysqli->affected_rows >= 1) {
       echo $fieldNames[followupApptSet]." ".$followupDate;
	} else {
		echo $fieldNames[updateFailed];
	} 
  } else {
  $mysqli->query("UPDATE `".$moduleDB."`.`patientvisit` SET `".$mysqlField."` =  '".$mysqlValue."' , `providerId` = '".$_SESSION['amomsId']."' WHERE `appointmentId` = '".$mysqlRowId."' LIMIT 1");
  if ($mysqli->affected_rows >= 1) {
    if (  $mysqlField == 'treatmentCode' ) { 
	  $query2 = "SELECT `charge` FROM `".$moduleDB."`.`treatmentCode` WHERE `treatmentCode` =  '".$mysqlTreatmentCode."'";
      $result2 = $mysqli->query($query2);
      $row2 = $result2->fetch_object(); 
		if ($result2->num_rows >= 1) {
		  $query3 = "UPDATE `".$moduleDB."`.`patientvisit` SET `balance` =  '".$row2->charge."' - (`charge` - `balance`)  , `charge` =  '".$row2->charge."'  WHERE `appointmentId` = '".$mysqlRowId."' LIMIT 1";
		 $result3 = $mysqli->query($query3);
				
	    $query4 = "SELECT * FROM `".$moduleDB."`.`patientbilling` WHERE `appointmentId` =  '".$mysqlRowId."'";
        $result4 = $mysqli->query($query4);
		  if ($result4->num_rows >=1) {
		    $query5 = "INSERT INTO `".$moduleDB."`.`patientbilling` (`transactionType` , `patientId`, `appointmentId`, `transDate`, `payment` ) 
		    VALUES ('appt', '".$mysqlPatient."', '".$mysqlRowId."', CURDATE(), '".$row2->charge."')";
		    $result4 = $mysqli->query($query5);		
	  	  } else {	
		    $query5 = "UPDATE `".$moduleDB."`.`patientbilling` SET  `payment` =  '".$row2->charge."' WHERE `appointmentId` =  '".$mysqlRowId."'";
		    $result5 = $mysqli->query($query5);
		  }	
		} else {
		  echo "<span class=\"error\">ERROR: Could not add cost information</span><br />\n";
		}
	}
  }
	  
    if ($languagePref == 'spa') {
	  echo "Cita ".$mysqlRowId." ".$fieldNames[$mysqlField]." ajustado a ".$mysqlValue.".<br />\n";
	} else {	
	  echo "Appointment ".$mysqlRowId." ".$mysqlField." set to ".$mysqlValue.".<br />\n";
	}
  }
?>
