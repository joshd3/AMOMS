<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Billing/Facturación</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('billing').className = 'active';
  };
</script>

<?php
 include "header.php";
 include "include/includeTranslation.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessPatientAcctMgr == 0 && $accessOfficeMgr == 0 && $accessReceptionist == 0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de facturación. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Billing Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 
	
	//Identify account balance
	$query = "SELECT SUM(`payment`) AS `payment` 
	  FROM `".$moduleDB."`.`patientbilling` WHERE `patientId` = '".$patientId."'";
	$result = $mysqli->query($query);
    $row = $result->fetch_object(); 
	$paymentBalance = $row->payment;
	$result->close;
	
	//Identify oldest unpaid appointment
	$query = "SELECT DATEDIFF(CURDATE() , MIN(`appointmentDate`)) AS `dayspastdue`  
	  FROM `".$moduleDB."`.`patientvisit` WHERE `patientId` = '".$patientId."' AND `balance` > 0 ORDER BY `appointmentDate` DESC";
	$result = $mysqli->query($query);
    $row = $result->fetch_object(); 
	$dayspastdue = $row->dayspastdue;
	
	echo "<div><span id=\"acctBalance\" name=\"acctBalance\" class=\"acctBalance\">".$fieldNames[acctBalance].": ".$paymentBalance."</span>\n";
	echo "<span id=\"acctStatusLabel\" name=\"acctStatusLabel\" class=\"acctStatus\" style=\"margin-left:10em;\">".$fieldNames[acctStatus]."</span>\n";
	//Display status span based on days pastdue
	if ($dayspastdue >= 120) {	echo "<span id=\"acctStatus120\" name=\"acctStatus120\" class=\"acctStatus, acctStatus120\">120+</span>\n"; 
    } elseif ($dayspastdue >= 90) {	echo "<span id=\"acctStatus90\" name=\"acctStatus90\" class=\"acctStatus, acctStatus90\">90</span>\n";  
	} elseif ($dayspastdue >= 60) {	echo "<span id=\"acctStatus60\" name=\"acctStatus60\" class=\"acctStatus, acctStatus60\">60</span>\n";  
	} elseif ($dayspastdue >= 30) {	echo "<span id=\"acctStatus30\" name=\"acctStatus30\" class=\"acctStatus, acctStatus30\">30</span>\n"; 
	} else {	echo "<span id=\"acctStatus0\" name=\"acctStatus0\" class=\"acctStatus, acctStatus0\">0</span>\n"; } 
	echo "</div><hr />\n";	
	$result->close;
	
	
	//Pull Days with Balance Due
	$query = "SELECT `appointmentDate`, `balance` , `appointmentId`
	  FROM `".$moduleDB."`.`patientvisit` WHERE `patientId` = '".$patientId."' AND `balance` > 0 ORDER BY `appointmentDate` DESC";
	$result = $mysqli->query($query);
    while ($row = $result->fetch_object()) {
	  $appointmentId = $row->appointmentId;
	  $visitBalanceArray[] = array('appointmentDate' => $row->appointmentDate, 'appointmentId' => $appointmentId, 'balance' => $row->balance );
      $appointmentValidationArray[] = $appointmentId;
	}
	//Process Payment
	if ($_POST['paymentSubmit']) {
	  //Validate Appointment ID
	  $checksum = 0;
	  $message = '';
	  if (in_array($_POST['apptSelect'], $appointmentValidationArray))         { $mysqliApptId =  $_POST['apptSelect']; }       else { $checksum++; $message .= $fieldNames[invalidApptId]."  ";}
	  if (array_key_exists($_POST['paymentType'], $paymentTypeArray))          { $mysqliPaymentType =  $_POST['paymentType']; } else { $checksum++; $message .= $fieldNames[invalidPaymentType]."  ";}
	  if (array_key_exists($_POST['payor'], $payorArray))                      { $mysqliPayor =  $_POST['payor']; }             else { $checksum++; $message .= $fieldNames[invalidPayor]." ";}
	  if (floatval($_POST['payment']) > 0)                                     { $mysqliPayment = abs(floatval($_POST['payment'])); }          else { $checksum++; $message .= $fieldNames[invalidPayment]." ";}
	
	  //Cap account credit payment type to the amount of the overall account over payment	
	  if ($mysqliPaymentType == 'acctCredit' && -$paymentBalance < $mysqliPayment	) { $mysqliPayment = -$paymentBalance; $message .= $fieldNames[paymentCapped]." ";}
      echo $message;
		
	  if ($checksum == 0) { //Apply payment
        //Check balance for appointment Date
	    $query2 = "SELECT `balance` FROM `".$moduleDB."`.`patientvisit` WHERE `appointmentId` = '".$mysqliApptId."' LIMIT 1";
	    $result2 = $mysqli->query($query2);
        $row2 = $result2->fetch_object(); 
	    $balance = $row2->balance;
		$result2->close();
		if ($balance >=  $mysqliPayment) { //Apply full amount of payment to that appointment 
		  //Update Patient Visit Table
	      $query2 = "UPDATE `".$moduleDB."`.`patientvisit` SET `balance` = `balance` - ".$mysqliPayment." WHERE `appointmentId` = '".$mysqliApptId."' LIMIT 1";
		  $result2 = $mysqli->query($query2);
		  
		  echo $mysqliPayment." ".$fieldNames[paymentApplied]." ".$mysqliApptId."<br />\n";
		   
		} else {
		//Pay Current Date, then loop through oldest appointments to pay balance.  Apply account credit if no remaing appointments
          //Zero balance on selected appointment Id
	      $query2 = "UPDATE `".$moduleDB."`.`patientvisit` SET `balance` = 0 WHERE `appointmentId` = '".$mysqliApptId."' LIMIT 1";
		  $result2 = $mysqli->query($query2);
			
		  echo $balance." ".$fieldNames[paymentApplied]." ".$mysqliApptId."<br />\n";
		  $mysqliPayment2 = $mysqliPayment - $balance; //update payment remaining
   	
		  while ($mysqliPayment2 > 0) {
			$query2 = "SELECT `balance` , `appointmentId` FROM `".$moduleDB."`.`patientvisit` WHERE `balance` > 0 ORDER BY `appointmentDate` ASC LIMIT 1";
	        $result2 = $mysqli->query($query2);
			if ($result2->num_rows == 0) { //exit loop if no appointment with balance due, this will create a negative account balance below
			  echo "Account has a credit of ".$mysqliPayment2."<br />\n";
     		  break; //end loop
			}	
	        $row2 = $result2->fetch_object(); 
	        $balance = $row2->balance;
			$appointmentId = $row2->appointmentId;		  
            
			  
			  
			if ($balance >=  $mysqliPayment2) { //Apply full amount of payment to that appointment 
	          $query3 = "UPDATE `".$moduleDB."`.`patientvisit` SET `balance` = `balance` - ".$mysqliPayment2." WHERE `appointmentId` = '".$appointmentId."' LIMIT 1";
		      $result3 = $mysqli->query($query3);
			  echo $mysqliPayment2." ".$fieldNames[paymentApplied]." ".$appointmentId."<br />\n";
		      break; //end loop
			} else {
	          $query3 = "UPDATE `".$moduleDB."`.`patientvisit` SET `balance` = 0 WHERE `appointmentId` = '".$appointmentId."' LIMIT 1";
		      $result3 = $mysqli->query($query3);
				
			  echo $balance." ".$fieldNames[paymentApplied]." ".$appointmentId."<br />\n";
			  $mysqliPayment2 = $mysqliPayment2 - $balance;
			}
			echo $mysqliPayment2."<br />\n";
			$result2->close;
		  }
			
		}
	  
  
		//Update Transaction Table
		if ($mysqliPaymentType != 'acctCredit') {
	      $query2 = "INSERT INTO `".$moduleDB."`.`patientbilling` (`transactionType`, `patientId`, `appointmentId`, `transDate`, `payment`, `payor`, `paymentRecdBy`) VALUES
		  ('".$mysqliPaymentType."', '".$patientId."', '".$mysqliApptId."',  CURDATE(), -".$mysqliPayment.", '".$mysqliPayor."', '".$_SESSION['amomsId']."')";
		  $result2 = $mysqli->query($query2);
		  $result2->close();
		  
		  echo $mysqliPayment." payment received<br />\n";
		} else {  //using an account Credit, adds the amount of the credit to the patient account rather than subtract
		  $query2 = "INSERT INTO `".$moduleDB."`.`patientbilling` (`transactionType`, `patientId`, `appointmentId`, `transDate`, `payment`, `payor`, `paymentRecdBy`) VALUES
		  ('".$mysqliPaymentType."', '".$patientId."', '".$mysqliApptId."',  CURDATE(), ".$mysqliPayment.", '".$mysqliPayor."', '".$_SESSION['amomsId']."')";
		  $result2 = $mysqli->query($query2);
		  $result2->close();
			
		  echo $mysqliPayment." ".$fieldNames[creditApplied]."<br />\n";
		}
	  } else { // echo error message	
        echo "<p>".$message."</p>\n";
	  }
	}
	
	//Payment Form
	echo "<div>\n";
	echo "  <form id=\"acctPayment\" method=\"post\" action=\"billing.php?pid=".$patientId."\">\n";
	echo "    <p>\n";
	echo "      <label for=\"apptSelect\">".$fieldNames[apptDate]."</label><select id=\"apptSelect\" name=\"apptSelect\" >\n";
    echo "      <option  ></option>\n";
	foreach ($visitBalanceArray AS $value) {
	  print_r($value);
	  echo "      <option value=\"".$value[appointmentId]."\" title=\"".$value[balance]."\">".$value[appointmentDate]."</option>\n";
	}
	echo "      </select>\n";
	
	echo "      <label for=\"payment\">".$fieldNames[paymentAmount]."</label><input type=\"text\" id=\"payment\" name=\"payment\" value=\"0.00\">\n"; 
	
	echo "      <label for=\"paymentType\">".$fieldNames[paymentType]."</label><select id=\"paymentType\" name=\"paymentType\" >\n";
	echo "      <option  ></option>\n";
	foreach ($paymentTypeArray AS $key=>$value) {
	  echo "      <option value=\"".$key."\" title=\"".$value."\">".$key."</option>\n";
	}
	echo "      </select>\n";
	
	echo "      <label for=\"payor\">".$fieldNames[payor]."</label><select id=\"payor\" name=\"payor\" >\n";
	echo "      <option  ></option>\n";
	foreach ($payorArray AS $key=>$value) {
	  echo "      <option value=\"".$key."\" title=\"".$value."\">".$key."</option>\n";
	}
	echo "      </select>\n";
    echo "      <input type=\"submit\" value=\"".$fieldNames[paymentSubmit]."\" id=\"paymentSubmit\" name=\"paymentSubmit\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
	echo "</div>\n";
	
	$result->close;
	
//Display Patient Payment History	
if  ($accessPatientAcctMgr == 1 || $accessOfficeMgr == 1) {
	//Field Array
	$headerArray = array('apptDate' , 'providerId' , 'diagnosticCode' , 'treatmentCode' , 'charge' , 'transDate' , 'transType' , 'payment' , 'payor');
	//Display existing treatments
	echo "<h3>".$fieldNames[paymentHistory]."</h3>\n";
	$query = "SELECT `patientvisit`.`appointmentDate` AS `apptDate`, `patientvisit`.`insuranceBilled` AS `insuranceBilled`, `patientvisit`.`appointmentId` AS `appointmentId`, `patientvisit`.`providerId` AS `providerId`,
	`patientvisit`.`diagnosticCode` AS `diagnosticCode`, `patientvisit`.`treatmentCode` AS `treatmentCode`, 
	`patientvisit`.`charge` AS `charge`, `patientbilling`.`transDate` AS `transDate`, 
	`patientbilling`.`payment` AS `payment` ,`patientbilling`.`transactionType` AS `transType`, `patientbilling`.`payor` AS `payor`,
	`patientbilling`.`transactionId` AS `transactionId`
	FROM `".$moduleDB."`.`patientbilling` 
	INNER JOIN  `".$moduleDB."`.`patientvisit` ON  `patientbilling`.`appointmentId` =`patientvisit`.`appointmentId`
	WHERE `patientbilling`.`patientId` = '".$patientId."' ORDER BY `apptDate` DESC,`transDate` DESC";
    $result = $mysqli->query($query);
	
	echo "<table>\n";
	echo "  <tr>\n";
	echo "    <th>".$fieldNames[transactionId]."</th>\n";
	
	//Set headers with sort functions
	foreach ($headerArray AS $value) {
	    echo "    <th>".$fieldNames[$value]."</th>\n";
	}
    echo "  </tr>\n";
    while ($row = $result->fetch_object()) {
	  echo "  <tr>\n"; 
	  echo "    <td>".$row->transactionId."</td>\n";
	$editableArray = array('transType' );
      foreach ($headerArray AS $value) {
		if ($value == transType) {$row->$value = $paymentTypeArray[$row->$value];}
		if ($value == payor) {$row->$value = $payorArray[$row->$value];}
		if (in_array($value, $editableArray) && $row->transactionType == 'insuranceEstimate') {
    	  echo "    <td><input id=\"".$value.$row->transactionId."\" name=\"".$value.$row->transactionId."\" value=\"".$row->$value."\"	
	      onblur=\"updateTransaction('".$row->transactionId."', '".$value."');\"/></td>\n";
		} else {
    	  echo "    <td>".$row->$value."</td>\n";
		}
	  }
		if ($row->insuranceBilled == 0 && $insuranceApptId != $row->appointmentId) {
    	    echo "    <td><input type=\"submit\" id=\"insuranceSubmit".$row->appointmentId."\" onclick=\"insuranceSubmit('".$row->appointmentId."');\" value=\"".$fieldNames[insuranceSubmit]."\"/></td>\n";			
			$insuranceApptId = $row->appointmentId; //Set variable so cell only appears once.
		}
        echo "  </tr>\n";
	}
	echo "</table>\n";
	
} //end ($accessPatientAcctMgr == 1 || $accessOfficeMgr == 1) {	
	
} //end Authorized Access
include "footer.php";
?>
