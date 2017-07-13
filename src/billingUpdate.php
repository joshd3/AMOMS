<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
$AMOMSBillingEmail = 'jay.forrest@gatech.edu';
 
//Clean Text inputs
  $cleanRowId = htmlentities($_POST['rowId'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
if ($mysqlField == 'insuranceSubmit') {
  $query = "SELECT `patientvisit`.`treatmentCode` AS `treatmentCode`, `patientvisit`.`diagnosticCode` AS `diagnosis` , `patientvisit`.`appointmentDate` AS `treatmentDate`, 
    `patient`.`nameFirst` AS `nameFirst`, `patient`.`nameLast` AS `nameLast`, `patient`.`insuranceName`,    `patient`.`insurancePrimary` AS `insurancePrimary`, 
	`patient`.`insuranceGroupNo` AS `insuranceGroupNo`, `patient`.`insuranceNo` AS `insuranceNo`, `patient`.`insuranceEmail` AS `insuranceEmail` , `patientvisit`.`appointmentId` AS `apptId`
    FROM `patientvisit`
    INNER JOIN `patient` ON `patientvisit`.`patientId`=`patient`.`patientId`
    WHERE `patientvisit`.`appointmentId` = '".$mysqlRowId."'";
	
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	
	$header = "From: ".$AMOMSBillingEmail;
	$subject = "AMOMS Clinic Insurance Reimbursement Request / Solicitud de Reembolso de Seguro AMOMS Clinica";
	$body = "AMOMS Clinic Insurance Reimbursement Request / Solicitud de Reembolso de Seguro AMOMS Clinica\r\n\r\n";
	$body .= "Patient / Paciente: ".$row->nameFirst." ".$row->nameLast."\r\n\r\n";
	$body .= "Insurance Group ID / ID del grupo de seguros: ".$row->insuranceGroupNo."\r\n";
	$body .= "Insurance Policy Number / Número de póliza de seguro: ".$row->insuranceNo."\r\n";
	$body .= "Insurance Primary / Seguros Primaria: ".$row->insurancePrimary."\r\n\r\n";
	$body .= "Treatment Date / Fecha de Tratamiento: ".$row->treatmentDate."\r\n";
	$body .= "Diagnosis / Diagnóstico: ".$row->diagnosis."\r\n";	
	$body .= "Treatment Code / Código de Tratamiento: ".$row->treatmentCode."\r\n\r\n";	
	$body .= $fieldNames[AMOMSContact]."\r\n";	
	mail($row->insuranceEmail, $subject, wordwrap($body,120) , $header);
	echo $fieldNames[billSent];
	
	$queryUpdate = "UPDATE `".$moduleDB."`.`patientvisit` SET `insuranceBilled` = 1 WHERE `appointmentId` = '".$row->apptId."'";
    $resultUpdate = $mysqli->query($queryUpdate);
    echo $queryUpdate;
	
    $result->close;
	
} elseif ($mysqlField == 'emailBill') {
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
	$query = "SELECT SUM(`patientvisit`.`balance`) AS `balance` FROM `".$moduleDB."`.`patientvisit` WHERE `patientId` = '".$mysqlRowId."' AND `patientvisit`.`balance` > 0";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $acctBalance = $row->balance;
	$result->close;
	
	//Select Patient Bill Information
	$query = "SELECT  `patientvisit`.`appointmentDate` AS `apptDate`, `patientvisit`.`providerId` AS `providerId`,
	`patientvisit`.`diagnosticCode` AS `diagnosticCode`, `patientvisit`.`treatmentCode` AS `treatmentCode`, 
	`patientvisit`.`charge` AS `charge`, `patientvisit`.`balance` AS `balance`, `patientbilling`.`transDate` AS `transDate`, 
	`patientbilling`.`payment` AS `payment` ,`patientbilling`.`transactionType` AS `transType`, `patientbilling`.`payor` AS `payor`,
	`patientbilling`.`transactionId` AS `transactionId`
	FROM `".$moduleDB."`.`patientbilling` 
	INNER JOIN  `".$moduleDB."`.`patientvisit` ON  `patientbilling`.`appointmentId` =`patientvisit`.`appointmentId`
	WHERE `patientbilling`.`patientId` = '".$mysqlRowId."' AND `patientvisit`.`balance` > 0  ORDER BY `apptDate` DESC,`transDate` DESC";
    $result = $mysqli->query($query);
	
	if (strlen($patientNamePreferred) == 0 ) {	
	  $body = $patientNameLast." ".$patientNameFirst."\r\n\r\n".$fieldNames[pastDue];
	} else {
	  $body = $patientNameLast." ".$patientNamePreferred."\r\n\r\n".$fieldNames[pastDue];
	}
	$body .= ": ".$row->balance.". ".$fieldNames[pleaseRemit].".\r\n";
    while ($row = $result->fetch_object()) {
  	  $body .= "\r\n".$fieldNames[apptDate].": ".$row->apptDate."|".$fieldNames[provider].": ".ucwords(substr(substr($row->providerId, 0, -4), 1))."|".$fieldNames[apptCharge].": ".$row->charge."|".$fieldNames[currentBalance].": ".$row->balance;
	}
    $body .= $fieldNames[emailClose]."\r\n".$fieldNames[AMOMSContact];
	$result->close;		
	$subject = $fieldNames[emailBillingSubject];
	$header = "From: ".$AMOMSBillingEmail;
	mail($patientEmail, $subject, wordwrap($body,120) , $header);
	echo $fieldNames[billSent];
    $query = "UPDATE `".$moduleDB."`.`patient` SET `lastContactAttempt` =  CURDATE() , `contactNote`=CONCAT(`contactNote`,'\n',CURDATE(),' ".$fieldNames[billSent]."')  WHERE `patientId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	 echo $query;
	
} elseif ($mysqlField == 'contactNote') {
    $query = "UPDATE `".$moduleDB."`.`patient` SET `".$mysqlField."` =  '".$mysqlValue."' WHERE `patientId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlField." ".$fieldNames[textInputUpdate1]." ".$mysqlValue." ".$fieldNames[textInputUpdate2]." ".$mysqlRowId;
	} else {
		echo $fieldNames[updateFailed];
	} 	
	
} else {
    $query = "UPDATE `".$moduleDB."`.`patientBilling` SET `".$mysqlField."` =  '".$mysqlValue."' WHERE `treatmentId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlField." ".$fieldNames[textInputUpdate1]." ".$mysqlValue." ".$fieldNames[textInputUpdate2]." ".$mysqlRowId;
	} else {
		echo $fieldNames[updateFailed];
	}
}
