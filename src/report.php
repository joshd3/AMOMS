<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Reports/Informes</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('reports').className = 'active';
  };
</script>

<?php
 include "header.php";
 include "include/includeTranslation.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessOfficeMgr == 0 && $accessReceptionist == 0 && $accessProvider == 0 && $accessPatientAcctMgr == 0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de Informes. Póngase en contacto con el administrador de Office o administrador de sistema para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Reports Module.  Please contact the Office Manager or System Administrator for assistance.</h2>\n";
  }
} else { //begin Authorized Access	
  echo "    <div id=\"sidebar\" class=\"sidebar\">\n";
  if ($accessReceptionist == 1) {
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=callBack\">".$fieldNames[callBack]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=dailyApptReminder\">".$fieldNames[dailyApptReminder]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=weeklyApptReminder\">".$fieldNames[weeklyApptReminder]."</a></span>\n"; 
	echo "<hr />\n";  
  }
  if ($accessProvider == 1) {
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?pid=".$patientId."&name=internalReferral\">".$fieldNames[internalReferral]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?pid=".$patientId."&name=externalReferral\">".$fieldNames[externalReferral]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?pid=".$patientId."&name=medicalExcuse\">".$fieldNames[medicalExcuse]."</a></span>\n"; 
	echo "<hr />\n";  
  }
  if ($accessPatientAcctMgr == 1) {
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=30days\">".$fieldNames['30days']."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=60days\">".$fieldNames['60days']."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=90days\">".$fieldNames['90days']."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=120days\">".$fieldNames['120days']."</a></span>\n"; 
	echo "<hr />\n";  
  }
  if ($accessOfficeMgr == 1) {
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=120days\">".$fieldNames['120days']."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=profitLoss\">".$fieldNames[profitLoss]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=billableHours\">".$fieldNames[billableHours]."</a></span>\n"; 
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=weeklyPatientsbyProvider\">".$fieldNames[weeklyPatientsbyProvider]."</a></span>\n";   
    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=weeklyRevenueProvider\">".$fieldNames[weeklyRevenueProvider]."</a></span>\n";  
//    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=importASPEL\">".$fieldNames[importASPEL]."</a></span>\n"; 
//    echo "    <span class=\"report\"><a class=\"buttonlink\" href=\"report.php?name=exportASPEL\">".$fieldNames[exportASPEL]."</a></span>\n"; 	
	echo "<hr />\n";  
  }
  echo "    </div>\n"; //end div sidebar
  echo "    <div id=\"report\">\n";
  ///////////////////////////////////////////////////////////////
  //Call Back Report
  if ($_GET[name] == 'callBack' && $accessReceptionist == 1) {
	$query = "SELECT `patientvisit`.`patientId` AS `patientId`, `patient`.`nameLast` AS `nameLast` , `patient`.`nameFirst` AS `nameFirst` , `patient`.`namePreferred` AS `namePreferred`,
	  `patient`.`phone` AS `phone`,  `patientvisit`.`callBackDate` AS `callBackDate`
      FROM `patientvisit`
       INNER JOIN `patient` ON `patientvisit`.`patientId`=`patient`.`patientId`
         WHERE `patientvisit`.`callBackDate` >= NOW() AND `patientvisit`.`callBackDate` < NOW() + INTERVAL 1 MONTH ORDER BY `patientvisit`.`callBackDate` ASC";
	  $result = $mysqli->query($query);
	  
	  echo "<table>\n";
	  echo " <tr><th>ID</th><th>".$fieldNames[nameLast]."</th><th>".$fieldNames[namePreferred]."</th><th>".$fieldNames[phone]."</th><th>".$fieldNames[callBackDate]."</th></tr>\n";
      while ($row = $result->fetch_object()) {
	  echo "  <tr><td>".$row->patientId."</td><td>".$row->nameLast."</td>\n";
	  if (strlen($row->namePreferred) > 0 ) {
	    echo "    <td>".$row->namePreferred."</td>\n";
	  } else {
	    echo "    <td>".$row->nameFirst."</td>\n";	
	  }	  
      echo "    <td>".$row->phone."</td><td>".$row->callBackDate."</td></tr>\n";		  
	  }	  
	  $result->close;	
	  echo "</table>\n";
  
  }
  ///////////////////////////////////////////////////////////////
  //Daily Appt Reminder
  if (($_GET[name] == 'dailyApptReminder' || $_GET[name] == 'weeklyApptReminder' )&& $accessReceptionist == 1) {
	if ($_GET[name] == 'dailyApptReminder') {
	//Get Date of Next Business Day 
	if (date("w") == 6) { //Add two days if Saturday
	  $queryDate = date('Y-m-d', strtotime("+2 days"));
	} else {
	  $queryDate = date('Y-m-d', strtotime("+1 days"));
	}
	
	}
	if ($_GET[name] == 'weeklyApptReminder') {
	  $queryDate = date('Y-m-d', strtotime("+7 days"));
	}
	  
	$queryDate = '2017-04-17';	//Override Query Date
	echo "<h3>".$queryDate."</h3>\n";
	  
	  
	//List of Possible Appt Times  
	$hoursArray = array('05:30' , '06:00' , '06:30' , '07:00' , '07:30' , '08:00' , '08:30' , '09:00' , '09:30' , 
	  '10:00' , '10:30' , '11:00' , '11:30' , '12:00' , '12:30' , '13:00' , '13:30' , '14:00' , '14:30' , 
	  '15:00' , '15:30' , '16:00' , '16:30' , '17:00' , '17:30' , '18:00' , '18:30' , '19:00' , '19:30' , 
	  '20:00' , '20:30' , '21:00' );
	  
   //Get a list of providers
	  $query = "SELECT DISTINCT `userId` AS `provider` FROM `".$moduleDB."`.`user` WHERE `provider` = 1 ORDER BY `nameLast` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$providerArray[] = $row->provider;
	  }	  
	  $result->close;
	  
	foreach ($providerArray AS $value) {
	  $startTime = ''; //placeholder
	  $query = "SELECT * FROM `calendar".$value."` WHERE `date` = '".$queryDate."'";
	  $result = $mysqli->query($query);
	  $row = $result->fetch_object();
  	  echo "<h3>".$value."</h3>\n";
      echo "<table>\n";
      foreach ($hoursArray AS $value1) {
		 if (strlen($row->$value1) > 1 ) { //1st digit of calendar is a code bit, so ignore 
			 if (!$startTime) {$startTime = $value1;} // set startTime on First Value
			 
			 if ( $row->$startTime == $row->$value1) { // don't do anything yet
			 } else { //write row when the values don't match
                 //Get Patient Info
	             $queryPatient = "SELECT * FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".substr($row->$value1, 1)."'";
	             $resultPatient = $mysqli->query($queryPatient);
                 while ($rowPatient = $resultPatient->fetch_object()) {
			  	    echo "  <tr>\n    <td>".$startTime."-".$value1."</td>\n    <td>".$rowPatient->patientId."</td>\n";
					if (strlen($rowPatient->namePreferred) > 0) {
			  	      echo "    <td>".$rowPatient->namePreferred."</td>\n";						
					} else {
			  	      echo "    <td>".$rowPatient->nameFirst."</td>\n";					
					}
			  	    echo "    <td>".$rowPatient->nameLast."</td>\n";
				  	echo "    <td><input type=\"submit\" id=\"apptReminderPhone".$rowPatient->patientId."\" name=\"apptReminderPhone".$rowPatient->patientId."\" 
					  onclick=\"apptReminderPhone('".$rowPatient->patientId."');\" value=\"".$rowPatient->phone."\"</td>\n";						
				  	echo "    <td><input type=\"submit\" id=\"apptReminderPhone".$rowPatient->patientId."\" name=\"apptReminderPhone".$rowPatient->patientId."\"
					 onclick=\"apptReminder('".$rowPatient->patientId."' , '".$startTime."-".$value1."' , '".$queryDate."', '".$value."');\" value=\".$fieldNames[apptReminderEmailSubmit].\"/></td>\n";					
			  	    echo "    <td>".$rowPatient->lastContactAttempt."</td>\n    <td colspan=\"7\">".$rowPatient->contactNote."</td>\n";						
				
			  	    echo "  </tr>\n";
	             }	  
	             $resultPatient->close;				
				 
			    $startTime = $value1; //reset startTime			 
			 }
			 // $dailyReminderArray[$value1] = substr($row->$value1, 1);
		 } else {
		    if ($startTime) {
                 //Get Patient Info
	             $queryPatient = "SELECT * FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".substr($row->$startTime, 1)."'";
	             $resultPatient = $mysqli->query($queryPatient);
                 while ($rowPatient = $resultPatient->fetch_object()) {
			  	    echo "  <tr>\n    <td>".$startTime."-".$value1."</td>\n    <td>".$rowPatient->patientId."</td>\n";
					if (strlen($rowPatient->namePreferred) > 0) {
			  	      echo "    <td>".$rowPatient->namePreferred."</td>\n";						
					} else {
			  	      echo "    <td>".$rowPatient->nameFirst."</td>\n";					
					}
			  	    echo "    <td>".$rowPatient->nameLast."</td>\n";
				  	echo "    <td><input type=\"submit\" id=\"apptReminderPhone".$rowPatient->patientId."\" name=\"apptReminderPhone".$rowPatient->patientId."\" 
					  onclick=\"apptReminderPhone('".$rowPatient->patientId."');\" value=\"".$rowPatient->phone."\"</td>\n";						
				  	echo "    <td><input type=\"submit\" id=\"apptReminderPhone".$rowPatient->patientId."\" name=\"apptReminderPhone".$rowPatient->patientId."\"
					 onclick=\"apptReminder('".$rowPatient->patientId."' , '".$startTime."-".$value1."' , '".$queryDate."', '".$value."');\" value=\"".$fieldNames[apptReminderEmailSubmit]."\"/></td>\n";					
			  	    echo "    <td>".$rowPatient->lastContactAttempt."</td>\n    <td colspan=\"7\">".$rowPatient->contactNote."</td>\n";						
				
			  	    echo "  </tr>\n";
	             }	  
	             $resultPatient->close;		
			}
			 unset($startTime); //unset startTime
		 }
	  }
	  
      echo "</table>\n";
		
		
	  echo "<hr />";
	}
	
	  
	  
	  
	  print_r($dailyReminderArray);
  }
  ///////////////////////////////////////////////////////////////
  //Weekly Appt Reminder
  ///////////////////////////////////////////////////////////////
  //Internal Referral
  if ($_POST[reportInternalReferralSubmit]) {
	  $defangArray = array('reportProviderSelect', 'reportReferralMessage');
	  foreach ($defangArray as $value) {
        ${'clean'.$value} = htmlentities($_POST[$value], ENT_COMPAT, 'UTF-8');
        ${'mysql'.ucwords($value)} = $mysqli->real_escape_string(${'clean'.$value});
	  }	 
	 
	//Select Referee email
	$query = "SELECT `email` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$mysqlReportProviderSelect."'";
	echo $query;
    $result = $mysqli->query($query);
	$row = $result->fetch_object();
	$to = $row->email;
	$result->close; 
	  
	//Select Referer Info
	$query = "SELECT `email` , `nameLast`, `nameFirst` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$_SESSION['amomsId']."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();
	  
	$subject = $fieldNames[reportInternalReferralHeader]." ".$patientId;
	$body = "Re:".$patientId."\r\n\r\n".$mysqlReportReferralMessage."\r\n\r\n".$fieldNames[emailClose]."\r\n".$row->nameFirst." ".$row->nameLast;
	$header = "From: ".$row->email;
	mail($to, $subject, wordwrap($body,70) , $header);
	echo "<h3>".$fieldNames[referralSent]."</h3>\n";
	echo "To: ".$to."<br />";
	echo $body."<br />";
	echo $header."<br />";
	$result->close;
	  
  }
  if ($_GET[name] == 'internalReferral' && $accessProvider == 1) {
    if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 	
	echo "<form id=\"internalReferral\" method=\"post\" action=\"report.php?pid=".$patientId."\">\n"; 
	  
	echo "  <p><label for=\"reportProviderSelect\">".$fieldNames[reportSelectProvider]."</label>
	<select id=\"reportProviderSelect\" name=\"reportProviderSelect\" >\n";
    echo "      <option  ></option>\n";
	//Select Doctor
	$query = "SELECT `userId`, `nameLast`, `nameFirst` FROM `".$moduleDB."`.`user` WHERE `provider` = 1 ORDER BY `nameLast`;";
    $result = $mysqli->query($query);	  //Type Message
    while ($row = $result->fetch_object()) {
	  echo "      <option value=\"".$row->userId."\">".$row->nameLast.", ".$row->nameFirst."</option>\n";
  	}
	$result->close;
	echo "      </select></p>\n";
	echo "      <p><label for=\"reportReferralMessage\">".$fieldNames[reportReferralMessage]."</label><br /><textarea rows=\"10\" cols=\"90\" id=\"reportReferralMessage\" name=\"reportReferralMessage\" >\n";
	echo "</textarea>\n";
	echo "      <input type=\"submit\" value=\"".$fieldNames[reportInternalReferralSubmit]."\" id=\"reportInternalReferralSubmit\" name=\"reportInternalReferralSubmit\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
  }
  ///////////////////////////////////////////////////////////////
  //External Referral
  if ($_POST[reportExternalReferralSubmit]) {
	  print_r($_POST);
	  
	  $defangArray = array('reportReferee', 'reportRefereeEmail', 'reportReferralMessage');
	  foreach ($defangArray as $value) {
        ${'clean'.$value} = htmlentities($_POST[$value], ENT_COMPAT, 'UTF-8');
        ${'mysql'.ucwords($value)} = $mysqli->real_escape_string(${'clean'.$value});
	  }	 
	//Select Patient Information
	$query = "SELECT `nameLast`, `nameFirst`, `patientId` FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$patientId."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $patientNameLast = $row->nameLast;
    $patientNameFirst = $row->nameFirst;	  
	$result->close;
	//Select Referer email
	$query = "SELECT `email` , `nameLast`, `nameFirst` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$_SESSION['amomsId']."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();
	  
	$subject = $fieldNames[reportExternalReferralHeader]." ".$patientId;
	$body = "Re:".$patientNameLast." ".$patientNameFirst."\r\n\r\n".$mysqlReportReferralMessage."\r\n\r\n".$fieldNames[emailClose]."\r\n".$row->nameFirst." ".$row->nameLast."\r\n".$fieldNames[AMOMSContact];
	$header = "From: ".$row->email;
	mail($mysqlReportRefereeEmail, $subject, wordwrap($body,70) , $header);
	echo "<h3>".$fieldNames[referralSent]."</h3>\n";
	echo "To: ".$mysqlReportRefereeEmail."<br />";
	echo $body."<br />";
	echo $header."<br />";
	$result->close;
	  
  }
  if ($_GET[name] == 'externalReferral' && $accessProvider == 1) {
    if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 	
	echo "<form id=\"externalReferral\" method=\"post\" action=\"report.php?pid=".$patientId."\">\n"; 
	echo "  <p>\n";
    echo "  <label for=\"reportReferee\">".$fieldNames[reportReferee]." </label><input type=\"text\" id=\"reportReferee\" name=\"reportReferee\" />\n";
    echo "  <label for=\"reportRefereeEmail\">".$fieldNames[reportRefereeEmail]." </label><input type=\"text\" id=\"reportRefereeEmail\" name=\"reportRefereeEmail\" /></p>\n";	  
	echo "      <p><label for=\"reportReferralMessage\">".$fieldNames[reportReferralMessage]."</label></p>\n    <textarea rows=\"10\" cols=\"90\" id=\"reportReferralMessage\" name=\"reportReferralMessage\" >\n";
	echo "</textarea>\n";
	echo "    <p><input type=\"submit\" value=\"".$fieldNames[reportExternalReferralSubmit]."\" id=\"reportExternalReferralSubmit\" name=\"reportExternalReferralSubmit\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
  }
	
  ///////////////////////////////////////////////////////////////
  //Medical Excuse Statement
  //Format Excuse	
  if ($_POST[reportMedicalExcuseSubmit]) {
	//Get Provider Information  
	$query = "SELECT `email` , `nameLast`, `nameFirst` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$_SESSION['amomsId']."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();
    $providerNameLast = $row->nameLast;
    $providerNameFirst = $row->nameFirst;	
    $providerEmail = $row->email;	
	$result->close;	  
	  
	//Get Patient Information
	$query = "SELECT `nameLast`, `nameFirst`, `patientId` FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$patientId."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $patientNameLast = $row->nameLast;
    $patientNameFirst = $row->nameFirst;	  
	$result->close;	  
	  echo "<h2>AMOMS</h2>";
	  echo "<h4>".$fieldNames[to].": ".htmlentities($_POST[excuseTo], ENT_COMPAT, 'UTF-8')."</h4>";
      echo "<h4>".$fieldNames[from].": ".$providerNameFirst." ".$providerNameLast."</h4>";
      echo "<h4>Re: ".$patientNameFirst." ".$patientNameLast."</h4>";
      echo "<h5>".$fieldNames[excuseFromDate].": ".htmlentities($_POST[excuseFromDate], ENT_COMPAT, 'UTF-8')."</h5>";
      echo "<h5>".$fieldNames[excuseToDate].": ".htmlentities($_POST[excuseToDate], ENT_COMPAT, 'UTF-8')."</h5>";
      echo "<p>".htmlentities($_POST[reportReferralMessage], ENT_COMPAT, 'UTF-8')."</p>";
	  echo "<p>".$fieldNames[emailClose]."<br />\n";
      echo $providerNameFirst." ".$providerNameLast."<br />\n";
      echo $providerEmail."<br />\n";
      echo $fieldNames[AMOMSContact]."</p>\n";
  
  }
	
  //Get excuse information
  if ($_GET[name] == 'medicalExcuse' && $accessProvider == 1) {
    if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 	
	//Get Patient Information
	$query = "SELECT `nameLast`, `nameFirst`, `patientId` FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$patientId."'";
    $result = $mysqli->query($query);
	$row = $result->fetch_object();	  
    $patientNameLast = $row->nameLast;
    $patientNameFirst = $row->nameFirst;	  
	$result->close;	  
	  
	echo "<form id=\"medicalExcuse\" method=\"post\" action=\"report.php?pid=".$patientId."\">\n"; 
	echo "  <p>\n";
    echo "  <label for=\"excuseTo\">".$fieldNames[excuseTo]." </label><input type=\"text\" id=\"excuseTo\" name=\"excuseTo\" />\n";
    echo "  <label for=\"excuseFromDate\">".$fieldNames[excuseFromDate]." </label><input type=\"text\" id=\"excuseFromDate\" name=\"excuseFromDate\" value=\"".date("Y-m-d")."\"/>\n";
    echo "  <label for=\"excuseToDate\">".$fieldNames[excuseToDate]." </label><input type=\"text\" id=\"excuseToDate\" name=\"excuseToDate\" value=\"".date("Y-m-d")."\"/>\n";
	echo "      <p><label for=\"reportReferralMessage\">".$fieldNames[reportMedicalExcuseMessage]."</label></p>\n    <textarea rows=\"10\" cols=\"90\" id=\"reportReferralMessage\" name=\"reportReferralMessage\" >\n";
	echo "Re: ".$patientNameFirst." ".$patientNameLast."\n".$fieldNames[standardMedicalExcuse]."</textarea>\n";
	echo "    <p><input type=\"submit\" value=\"".$fieldNames[reportMedicalExcuseSubmit]."\" id=\"reportMedicalExcuseSubmit\" name=\"reportMedicalExcuseSubmit\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";	    
  }
  ///////////////////////////////////////////////////////////////
  //30+ Days Past Due
  if ($_GET[name] == '30days' && $accessPatientAcctMgr == 1) {
    $query = "SELECT sub.`patientId` AS `patientId`, sub.`sumBalance` AS `sumBalance`, `patient`.`nameLast` AS `nameLast`, `patient`.`nameFirst` AS `nameFirst`, `patient`.`namePreferred` AS `namePreferred`,        
	  `patient`.`phone` AS `phone`, `patient`.`email` AS `email`, `patient`.`lastContactAttempt` AS `lastContactAttempt`, `patient`.`contactNote` AS `contactNote`
      FROM (
        SELECT `patientId` , DATEDIFF(CURDATE() , MIN(`appointmentDate`)) AS `dayspastdue` , sum(`balance`) AS `sumBalance`
        FROM `".$moduleDB."`.`patientvisit`
        WHERE `balance` > 0
      ) sub
      INNER JOIN `".$moduleDB."`.`patient` ON sub.`patientId`=`patient`.`patientId`
      WHERE sub.`dayspastdue` >=30 AND `dayspastdue` < 60";
    $result = $mysqli->query($query);
	if ($result->num_rows == 0) {
	  echo $fieldNames[no30DayReports];
	} else {
	  echo "  <table>\n";
	  echo "    <tr>\n      <th>".$fieldNames[patientId]."</th>\n      <th>".$fieldNames[acctBalance]."</th>\n      <th>".$fieldNames[patientNamePreferred]."</th>\n      <th>".$fieldNames[nameLast]."</th>\n";
	  echo "		<th>".$fieldNames[patientPhone]."</th>\n      <th>&nbsp;</th>\n    </tr>\n";
	  echo "    <tr>\n		<th>".$fieldNames[patientLastContactAttempt]."</th>\n      <th colspan=\"6\">".$fieldNames[contactNote]."</th>\n    </tr>\n";
      while ($row = $result->fetch_object()) {
	    echo "    <tr>\n";
	    echo "      <td>".$row->patientId."</td>\n      <td>".$row->sumBalance."</td>\n";
	    if (strlen($row->namePreferred) == 0) { 
	      echo "      <td>".$row->nameFirst."</td>\n";
	    } else {
	      echo "      <td>".$row->namePreferred."</td>\n";
        }
	    echo "      <td>".$row->nameLast."</td>\n      <td>".$row->phone."</td>\n";
	    echo "      <td><input type=\"submit\" id=\"emailBill".$row->patientId."\" onclick=\"emailBill('".$row->patientId."' , 'emailBill');\" value=\"".$fieldNames[patientEmailBill]."\"/></td>\n    </tr>\n";
	    echo "    <tr>\n		<td>".$row->lastContactAttempt."</td>\n";
	    echo "      <td colspan=\"6\"><textarea cols=\"70\" rows=\"2\" id=\"contactNote".$row->patientId."\"  onblur=\"updateContactNote('".$row->patientId."', 'contactNote');\" >".$row->contactNote."</textarea></th>\n    </tr>\n";	  
	  }
	  echo "  </table>\n";
	}
  }
	
  ///////////////////////////////////////////////////////////////
  //60+ Days Past Due
  if ($_GET[name] == '60days' && $accessPatientAcctMgr == 1) {
    $query = "SELECT sub.`patientId` AS `patientId`, sub.`sumBalance` AS `sumBalance`, `patient`.`nameLast` AS `nameLast`, `patient`.`nameFirst` AS `nameFirst`, `patient`.`namePreferred` AS `namePreferred`,        
	  `patient`.`phone` AS `phone`, `patient`.`email` AS `email`, `patient`.`lastContactAttempt` AS `lastContactAttempt`, `patient`.`contactNote` AS `contactNote`
      FROM (
        SELECT `patientId` , DATEDIFF(CURDATE() , MIN(`appointmentDate`)) AS `dayspastdue` , sum(`balance`) AS `sumBalance`
        FROM `".$moduleDB."`.`patientvisit`
        WHERE `balance` > 0
      ) sub
      INNER JOIN `".$moduleDB."`.`patient` ON sub.`patientId`=`patient`.`patientId`
      WHERE sub.`dayspastdue` >=60 AND `dayspastdue` < 90";
    $result = $mysqli->query($query);
	if ($result->num_rows == 0) {
	  echo $fieldNames[no60DayReports];
	} else {
	  echo "  <table>\n";
	  echo "    <tr>\n      <th>".$fieldNames[patientId]."</th>\n      <th>".$fieldNames[acctBalance]."</th>\n      <th>".$fieldNames[patientNamePreferred]."</th>\n      <th>".$fieldNames[nameLast]."</th>\n";
	  echo "		<th>".$fieldNames[patientPhone]."</th>\n      <th>&nbsp;</th>\n    </tr>\n";
	  echo "    <tr>\n		<th>".$fieldNames[patientLastContactAttempt]."</th>\n      <th colspan=\"6\">".$fieldNames[contactNote]."</th>\n    </tr>\n";
      while ($row = $result->fetch_object()) {
	    echo "    <tr>\n";
	    echo "      <td>".$row->patientId."</td>\n      <td>".$row->sumBalance."</td>\n";
	    if (strlen($row->namePreferred) == 0) { 
	      echo "      <td>".$row->nameFirst."</td>\n";
	    } else {
	      echo "      <td>".$row->namePreferred."</td>\n";
        }
	    echo "      <td>".$row->nameLast."</td>\n      <td>".$row->phone."</td>\n";
	    echo "      <td><input type=\"submit\" id=\"emailBill".$row->patientId."\" onclick=\"emailBill('".$row->patientId."' , 'emailBill');\" value=\"".$fieldNames[patientEmailBill]."\"/></td>\n    </tr>\n";
	    echo "    <tr>\n		<td>".$row->lastContactAttempt."</td>\n";
	    echo "      <td colspan=\"6\"><textarea cols=\"70\" rows=\"2\" id=\"contactNote".$row->patientId."\"  onblur=\"updateContactNote('".$row->patientId."', 'contactNote');\" >".$row->contactNote."</textarea></th>\n    </tr>\n";	  
	  }
	  echo "  </table>\n";
	}
  }
  ///////////////////////////////////////////////////////////////
  //90+ Days Past Due
  if ($_GET[name] == '90days' && $accessPatientAcctMgr == 1) {
    $query = "SELECT sub.`patientId` AS `patientId`, sub.`sumBalance` AS `sumBalance`, `patient`.`nameLast` AS `nameLast`, `patient`.`nameFirst` AS `nameFirst`, `patient`.`namePreferred` AS `namePreferred`,        
	  `patient`.`phone` AS `phone`, `patient`.`email` AS `email`, `patient`.`lastContactAttempt` AS `lastContactAttempt`, `patient`.`contactNote` AS `contactNote`
      FROM (
        SELECT `patientId` , DATEDIFF(CURDATE() , MIN(`appointmentDate`)) AS `dayspastdue` , sum(`balance`) AS `sumBalance`
        FROM `".$moduleDB."`.`patientvisit`
        WHERE `balance` > 0
      ) sub
      INNER JOIN `".$moduleDB."`.`patient` ON sub.`patientId`=`patient`.`patientId`
      WHERE sub.`dayspastdue` >=90 AND `dayspastdue` < 120";
    $result = $mysqli->query($query);
	if ($result->num_rows == 0) {
	  echo $fieldNames[no90DayReports];
	} else {
	  echo "  <table>\n";
	  echo "    <tr>\n      <th>".$fieldNames[patientId]."</th>\n      <th>".$fieldNames[acctBalance]."</th>\n      <th>".$fieldNames[patientNamePreferred]."</th>\n      <th>".$fieldNames[nameLast]."</th>\n";
	  echo "		<th>".$fieldNames[patientPhone]."</th>\n      <th>&nbsp;</th>\n    </tr>\n";
	  echo "    <tr>\n		<th>".$fieldNames[patientLastContactAttempt]."</th>\n      <th colspan=\"6\">".$fieldNames[contactNote]."</th>\n    </tr>\n";
      while ($row = $result->fetch_object()) {
	    echo "    <tr>\n";
	    echo "      <td>".$row->patientId."</td>\n      <td>".$row->sumBalance."</td>\n";
	    if (strlen($row->namePreferred) == 0) { 
	      echo "      <td>".$row->nameFirst."</td>\n";
	    } else {
	      echo "      <td>".$row->namePreferred."</td>\n";
        }
	    echo "      <td>".$row->nameLast."</td>\n      <td>".$row->phone."</td>\n";
	    echo "      <td><input type=\"submit\" id=\"emailBill".$row->patientId."\" onclick=\"emailBill('".$row->patientId."' , 'emailBill');\" value=\"".$fieldNames[patientEmailBill]."\"/></td>\n    </tr>\n";
	    echo "    <tr>\n		<td>".$row->lastContactAttempt."</td>\n";
	    echo "      <td colspan=\"6\"><textarea cols=\"70\" rows=\"2\" id=\"contactNote".$row->patientId."\"  onblur=\"updateContactNote('".$row->patientId."', 'contactNote');\" >".$row->contactNote."</textarea></th>\n    </tr>\n";	  
	  }
	  echo "  </table>\n";
	}
  }
  ///////////////////////////////////////////////////////////////
  //120+ Days Past Due
  if ($_GET[name] == '120days' && ($accessPatientAcctMgr == 1 || $accessOfficeMgr == 1)) {
    $query = "SELECT sub.`patientId` AS `patientId`, sub.`sumBalance` AS `sumBalance`, `patient`.`nameLast` AS `nameLast`, `patient`.`nameFirst` AS `nameFirst`, `patient`.`namePreferred` AS `namePreferred`,        
	  `patient`.`phone` AS `phone`, `patient`.`email` AS `email`, `patient`.`lastContactAttempt` AS `lastContactAttempt`, `patient`.`contactNote` AS `contactNote`
      FROM (
        SELECT `patientId` , DATEDIFF(CURDATE() , MIN(`appointmentDate`)) AS `dayspastdue` , sum(`balance`) AS `sumBalance`
        FROM `".$moduleDB."`.`patientvisit`
        WHERE `balance` > 0
      ) sub
      INNER JOIN `".$moduleDB."`.`patient` ON sub.`patientId`=`patient`.`patientId`
      WHERE sub.`dayspastdue` >=120";
    $result = $mysqli->query($query);
	if ($result->num_rows == 0) {
	  echo $fieldNames[no120DayReports];
	} else {
	  echo "  <table>\n";
	  echo "    <tr>\n      <th>".$fieldNames[patientId]."</th>\n      <th>".$fieldNames[acctBalance]."</th>\n      <th>".$fieldNames[patientNamePreferred]."</th>\n      <th>".$fieldNames[nameLast]."</th>\n";
	  echo "		<th>".$fieldNames[patientPhone]."</th>\n      <th>&nbsp;</th>\n    </tr>\n";
	  echo "    <tr>\n		<th>".$fieldNames[patientLastContactAttempt]."</th>\n      <th colspan=\"6\">".$fieldNames[contactNote]."</th>\n    </tr>\n";
      while ($row = $result->fetch_object()) {
	    echo "    <tr>\n";
	    echo "      <td>".$row->patientId."</td>\n      <td>".$row->sumBalance."</td>\n";
	    if (strlen($row->namePreferred) == 0) { 
	      echo "      <td>".$row->nameFirst."</td>\n";
	    } else {
	      echo "      <td>".$row->namePreferred."</td>\n";
        }
	    echo "      <td>".$row->nameLast."</td>\n      <td>".$row->phone."</td>\n";
	    echo "      <td><input type=\"submit\" id=\"emailBill".$row->patientId."\" onclick=\"emailBill('".$row->patientId."' , 'emailBill');\" value=\"".$fieldNames[patientEmailBill]."\"/></td>\n    </tr>\n";
	    echo "    <tr>\n		<td>".$row->lastContactAttempt."</td>\n";
	    echo "      <td colspan=\"6\"><textarea cols=\"70\" rows=\"2\" id=\"contactNote".$row->patientId."\"  onblur=\"updateContactNote('".$row->patientId."', 'contactNote');\" >".$row->contactNote."</textarea></th>\n    </tr>\n";	  
	  }
	  echo "  </table>\n";
	}
  }
  ///////////////////////////////////////////////////////////////
  //Profit/Loss
	
  if ($_POST[reportProfitLoss]) {
	$testYear = range(2000,2100);  
    if (in_array($_POST[resportYearSelect], $testYear)) {
		$mysqlYear = $_POST[resportYearSelect];
	} else {
		$mysqlYear = date("Y");
	}
  $i = 1;
  while ($i <=  12): 
	unset($transaction);
    $queryMonth = date("m", mktime(0,0,0, $i, 1, date("Y")));
    $result = $mysqli->query(
      "SELECT `transactionType` AS 'type' , sum(`payment`) AS 'payment'  FROM `".$moduleDB."`.`patientbilling` WHERE MONTH(`transDate`) = '".$queryMonth."' AND YEAR(`transDate`) = '".$mysqlYear."'  GROUP BY `type`
      ");
      $j = 1;
      $jmax = $result->num_rows;
      while ($j <= $result->num_rows):
        $row = $result->fetch_object(); 
        $transaction[$row->type] = $row->payment;
        $j++;
      endwhile;
    $transactionMonth[] = array(date("M", mktime(0, 0, 0, $i, 1, date("Y"))), $transaction[appt]+0, $transaction[cash]+0, $transaction[check]+0  , 
		$transaction[creditCard]+0 , $transaction[insuranceEstimate]+0 , $transaction[insurancePayment]+0 , $transaction[insuranceProviderCost]+0 , $transaction[acctCredit]+0 );
    $result->close();
  $i++;
  endwhile; 
  unset($transaction);
   
   echo "<h3>".$mysqlYear."</h3>\n";
   echo "<table>\n";
   echo "  <tr>\n    <th>".$fieldNames[month]."</th>\n    <th>".$fieldNames[apptCharge]."</th>\n    <th>".$paymentTypeArray[cash]."</th>\n    <th>".$paymentTypeArray[check]."</th>
       \n    <th>".$paymentTypeArray[creditCard]."</th>\n   <th>".$paymentTypeArray[insuranceEstimate]."</th>\n    <th>".$paymentTypeArray[insurancePayment]."</th>
	   \n    <th>".$paymentTypeArray[insuranceProviderCost]."</th>\n    <th>".$paymentTypeArray[acctCredit]."</th>\n  </tr>\n";
  $i = 0;
  while ($i <=  11):  
   echo "  <tr>\n    <td>".$transactionMonth[$i][0]."</td>\n    <td>".$transactionMonth[$i][1]."</td>\n    <td>".$transactionMonth[$i][2]."</td>\n    <td>".$transactionMonth[$i][3]."</td>
       \n    <td>".$transactionMonth[$i][4]."</td>\n   <td>".$transactionMonth[$i][5]."</td>\n    <td>".$transactionMonth[$i][6]."</td>
	   \n    <td>".$transactionMonth[$i][7]."</td>\n    <td>".$transactionMonth[$i][8]."</td>\n  </tr>\n";		  
  $i++;
  endwhile;
   echo "</table>\n";
	  
  }
  if ($_GET[name] == 'profitLoss' && $accessOfficeMgr == 1) {
	echo "<div id=\"reportSelect\">\n";
	  //Get Years available to report
	  $query = "SELECT DISTINCT YEAR(`transDate`) AS `year` FROM `".$moduleDB."`.`patientbilling` ORDER BY `year` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$yearArray[] = $row->year;
	  }
	  
	echo "<form id=\"profitLoss\" method=\"post\" action=\"report.php\">\n"; 
	echo "  <p>\n";
    echo "    <label for=\"reportYearSelect\">".$fieldNames[reportYearSelect]." </label>\n";
	echo "    <select id=\"reportYearSelect\" name=\"reportYearSelect\">\n";
	echo "      <option></option>\n";
	foreach ($yearArray AS $value) {
      echo "      <option>".$value."</option>";
	}
	echo "    </select>\n";
    echo "    <input type=\"submit\" value=\"".$fieldNames[requestReport]."\" id=\"reportProfitLoss\" name=\"reportProfitLoss\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
	echo "</div>\n";
  }
	
  ///////////////////////////////////////////////////////////////
  //Billable Hours
	
  if ($_POST[reportBillableHours]) {
	$testYear = range(2000,2100);  
    if (in_array($_POST[resportYearSelect], $testYear)) {
		$mysqlYear = $_POST[resportYearSelect];
	} else {
		$mysqlYear = date("Y");
	}
   echo "<h3>".$mysqlYear."</h3>\n";
   echo "<table>\n";
   echo "  <tr>\n    <th>".$fieldNames[month]."</th>\n    <th>".$fieldNames[billableHours]."</th>\n  </tr>\n";	
	  
   $query = "SELECT MONTH(`patientvisit`.`appointmentDate`) AS `month`, ROUND(SUM(`treatmentCode`.`duration`)/60 , 2) AS `hours`, `patientvisit`.`treatmentCode`, `patientvisit`.`providerId` FROM `patientvisit`
     INNER JOIN `treatmentCode` ON `treatmentCode`.`treatmentCode`=`patientvisit`.`treatmentCode`
     GROUP BY  MONTH(`patientvisit`.`appointmentDate`)";
    $result = $mysqli->query($query);
    while ($row = $result->fetch_object()) {
      echo "  <tr>\n    <td>".$row->month."</th>\n    <td>".$row->hours."</th>\n  </tr>\n";	
 	}
   echo "</table>\n";
 
  }
  if ($_GET[name] == 'billableHours' && $accessOfficeMgr == 1) {
	echo "<div id=\"reportSelect\">\n";
	  //Get Years available to report
	  $query = "SELECT DISTINCT YEAR(`transDate`) AS `year` FROM `".$moduleDB."`.`patientbilling` ORDER BY `year` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$yearArray[] = $row->year;
	  }
	  
	echo "<form id=\"billableHours\" method=\"post\" action=\"report.php\">\n"; 
	echo "  <p>\n";
    echo "    <label for=\"reportYearSelect\">".$fieldNames[reportYearSelect]." </label>\n";
	echo "    <select id=\"reportYearSelect\" name=\"reportYearSelect\">\n";
	echo "      <option></option>\n";
	foreach ($yearArray AS $value) {
      echo "      <option>".$value."</option>";
	}
	echo "    </select>\n";
    echo "    <input type=\"submit\" value=\"".$fieldNames[requestReport]."\" id=\"reportBillableHours\" name=\"reportBillableHours\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
	echo "</div>\n";
  }
  ///////////////////////////////////////////////////////////////
  //Patients by Provider (Week)
	
  if ($_POST[reportWeeklyPatientsbyProvider]) {
	$testYear = range(2000,2100);  
    if (in_array($_POST[resportYearSelect], $testYear)) {
		$mysqlYear = $_POST[resportYearSelect];
	} else {
		$mysqlYear = date("Y");
	}
    $i = 0;
	$maxPatientCount = 0; //Dummy for cell color coding
  while ($i <=  53): 
	unset($transaction);
    $queryMonth = date("m", mktime(0,0,0, $i, 1, date("Y")));
    $result = $mysqli->query(
      "SELECT   `user`.`userId` AS `user` , COUNT(`patientvisit`.`appointmentId`) AS `patients` FROM `user`
         LEFT JOIN `patientvisit` ON `patientvisit`.`providerId`=`user`.`userId`
         WHERE `user`.`provider` = 1 AND YEAR(`appointmentDate`) = '".$mysqlYear."' AND WEEK(`appointmentDate`) = ".$i."
          GROUP BY `user`.`userId`	  
      ");
      while ($row = $result->fetch_object()) {
        $transaction[$row->user] = $row->patients;
		if ($row->patients > $maxPatientCount) {$maxPatientCount = $row->patients;}  //adjust $maxPatientCount Count to highest weekly count of a single provider
	  }
	  
	    $date = new DateTime();
        $date->setISODate($mysqlYear,$i);	  
		$transactionWeek[$i][date] = $date->format('m-d');
	foreach ($transaction as $key=>$value) {
		$transactionWeek[$i][$key] = $value;
    }
    $result->close();
  $i++;
  endwhile; 
  unset($transaction);
	 //Get a list of providers
	  $query = "SELECT DISTINCT `userId` AS `provider` FROM `".$moduleDB."`.`user` WHERE `provider` = 1 ORDER BY `nameLast` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$providerArray[] = $row->provider;
	  }	  
	  $result->close;
	  
   echo "<h3>".$mysqlYear."</h3>\n";
   echo "<table>\n";
   echo "  <tr><th>".$fieldNames[provider]."\n";
   $i = 0;
   while ( $i < 54) {
      echo "    <th >".$transactionWeek[$i][date]."</th>\n";	
   $i++;
   }
   echo "  </tr>\n";
	  
   //Normalize $maxPatientCount to 255 RGB
   $normMaxPatientCount = 255 / $maxPatientCount;
	  
   foreach ($providerArray AS $value) {
     echo "  <tr><td>".$value."</td>\n";
     $i = 0;
     while ( $i < 54) {
		if ($transactionWeek[$i][$value] / $maxPatientCount == 1) {$color = "00FF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.875) {$color = "3FFF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.75) {$color = "7FFF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.625) {$color = "BFFF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.5) {$color = "FEFF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.375) {$color = "FFBF00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.25) {$color = "FF7F00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount >= 0.125) {$color = "FF3F00";}
		elseif ($transactionWeek[$i][$value] / $maxPatientCount > 0) {$color = "FFFF00";}
		else {$color = "000000";}
        echo "    <td style=\"background-color:#".$color.";\">".$transactionWeek[$i][$value]."</td>\n";	
     $i++;
     }
     echo "  </tr>\n";
   }
   echo "</table>\n";
	    
  }
  if ($_GET[name] == 'weeklyPatientsbyProvider' && $accessOfficeMgr == 1) {
	echo "<div id=\"reportSelect\">\n";
	  //Get Years available to report
	  $query = "SELECT DISTINCT YEAR(`transDate`) AS `year` FROM `".$moduleDB."`.`patientbilling` ORDER BY `year` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$yearArray[] = $row->year;
	  }
	  
	echo "<form id=\"weeklyPatientsbyProvider\" method=\"post\" action=\"report.php\">\n"; 
	echo "  <p>\n";
    echo "    <label for=\"reportYearSelect\">".$fieldNames[reportYearSelect]." </label>\n";
	echo "    <select id=\"reportYearSelect\" name=\"reportYearSelect\">\n";
	echo "      <option></option>\n";
	foreach ($yearArray AS $value) {
      echo "      <option>".$value."</option>";
	}
	echo "    </select>\n";
    echo "    <input type=\"submit\" value=\"".$fieldNames[requestReport]."\" id=\"reportWeeklyPatientsbyProvider\" name=\"reportWeeklyPatientsbyProvider\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
	echo "</div>\n";
  }
  ///////////////////////////////////////////////////////////////
  //Patients by Provider (Week)
	
  if ($_POST[reportWeeklyRevenueProvider]) {
	$testYear = range(2000,2100);  
    if (in_array($_POST[resportYearSelect], $testYear)) {
		$mysqlYear = $_POST[resportYearSelect];
	} else {
		$mysqlYear = date("Y");
	}
    $i = 0;
	$maxSumCharges = 0; //Dummy for cell color coding
  while ($i <=  53): 
	unset($transaction);
    $queryMonth = date("m", mktime(0,0,0, $i, 1, date("Y")));
    $result = $mysqli->query(
      "SELECT   `user`.`userId` AS `user` , SUM(`patientvisit`.`charge`) AS `charge` FROM `user`
         LEFT JOIN `patientvisit` ON `patientvisit`.`providerId`=`user`.`userId`
         WHERE `user`.`provider` = 1 AND YEAR(`appointmentDate`) = '".$mysqlYear."' AND WEEK(`appointmentDate`) = ".$i."
          GROUP BY `user`.`userId`	  
      ");
      while ($row = $result->fetch_object()) {
        $transaction[$row->user] = $row->charge;
		if ($row->charge > $maxSumCharges) {$maxSumCharges = $row->charge;}  //adjust $maxSumCharges to highest weekly sum of a single provider
	  }
	  
	    $date = new DateTime();
        $date->setISODate($mysqlYear,$i);	  
		$transactionWeek[$i][date] = $date->format('m-d');
	foreach ($transaction as $key=>$value) {
		$transactionWeek[$i][$key] = $value;
    }
    $result->close();
  $i++;
  endwhile; 
  unset($transaction);
	  
   //Get a list of providers
	  $query = "SELECT DISTINCT `userId` AS `provider` FROM `".$moduleDB."`.`user` WHERE `provider` = 1 ORDER BY `nameLast` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$providerArray[] = $row->provider;
	  }	  
	  $result->close;
	  
   echo "<h3>".$mysqlYear."</h3>\n";
   echo "<table>\n";
   echo "  <tr><th>".$fieldNames[provider]."\n";
   $i = 0;
   while ( $i < 54) {
      echo "    <th >".$transactionWeek[$i][date]."</th>\n";	
   $i++;
   }
   echo "  </tr>\n";
	  
	  
   foreach ($providerArray AS $value) {
     echo "  <tr><td>".$value."</td>\n";
     $i = 0;
     while ( $i < 54) {
		if ($transactionWeek[$i][$value] / $maxSumCharges == 1) {$color = "00FF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.875) {$color = "3FFF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.75) {$color = "7FFF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.625) {$color = "BFFF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.5) {$color = "FEFF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.375) {$color = "FFBF00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.25) {$color = "FF7F00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges >= 0.125) {$color = "FF3F00";}
		elseif ($transactionWeek[$i][$value] / $maxSumCharges > 0) {$color = "FFFF00";}
		else {$color = "000000";}
        echo "    <td style=\"background-color:#".$color.";\">".$transactionWeek[$i][$value]."</td>\n";	
     $i++;
     }
     echo "  </tr>\n";
   }
	  
	  
   echo "</table>\n";
	  
	  
  }
  if ($_GET[name] == 'weeklyRevenueProvider' && $accessOfficeMgr == 1) {
	echo "<div id=\"reportSelect\">\n";
	  //Get Years available to report
	  $query = "SELECT DISTINCT YEAR(`transDate`) AS `year` FROM `".$moduleDB."`.`patientbilling` ORDER BY `year` ASC";
	  $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
		$yearArray[] = $row->year;
	  }
	  
	echo "<form id=\"weeklyRevenueProvider\" method=\"post\" action=\"report.php\">\n"; 
	echo "  <p>\n";
    echo "    <label for=\"reportYearSelect\">".$fieldNames[reportYearSelect]." </label>\n";
	echo "    <select id=\"reportYearSelect\" name=\"reportYearSelect\">\n";
	echo "      <option></option>\n";
	foreach ($yearArray AS $value) {
      echo "      <option>".$value."</option>";
	}
	echo "    </select>\n";
    echo "    <input type=\"submit\" value=\"".$fieldNames[requestReport]."\" id=\"reportWeeklyRevenueProvider\" name=\"reportWeeklyRevenueProvider\" />\n";
	echo "    </p>\n";
	echo "  </form>\n";
	echo "</div>\n";
  }
 
  echo "    </div>\n"; //end div report
	
} //end Authorized Access
include "footer.php";
?>
