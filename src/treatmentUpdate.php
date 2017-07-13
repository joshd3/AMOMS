<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
 
//Clean Text inputs
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
$treatmentCodeOkay = 1;
if ($mysqlField == 'treatmentCode') { //Cannot modify treatment if it matches an existing treatment code
		$query = "SELECT * FROM `".$moduleDB."`.`treatmentCode` WHERE `treatmentCode` = '".$mysqlValue."'";
        $result = $mysqli->query($query);
		if ($result->num_rows > 0) { 
		  echo "<h2>".$fieldNames[treatmentExists]."</h2>"; 
          $result->close; 
		  $treatmentCodeOkay = 0;
	    } 
} 
if ($treatmentCodeOkay == 1) {
    $query = "UPDATE `".$moduleDB."`.`treatmentCode` SET `".$mysqlField."` =  '".$mysqlValue."' WHERE `treatmentId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlField." ".$fieldNames[textInputUpdate1]." ".$mysqlValue." ".$fieldNames[textInputUpdate2]." ".$mysqlRowId;
	} else {
		echo $fieldNames[updateFailed];
	}
}
