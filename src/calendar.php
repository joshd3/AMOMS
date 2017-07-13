<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Calendar/Calendario</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('calendar').className = 'active';
  };
</script>

<?php
 include "header.php";
 include "include/includeTranslation.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessProvider == 0 && $accessReceptionist == 0 && $accessOfficeMgr == 0 ) {
  if ($languagePref == 'spa') {
    echo "<h2>Usted no tiene acceso al calendario módulo. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Billing Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  
	
	$hoursArray = array('05:30' , '06:00' , '06:30' , '07:00' , '07:30' , '08:00' , '08:30' , '09:00' , '09:30' , 
	  '10:00' , '10:30' , '11:00' , '11:30' , '12:00' , '12:30' , '13:00' , '13:30' , '14:00' , '14:30' , 
	  '15:00' , '15:30' , '16:00' , '16:30' , '17:00' , '17:30' , '18:00' , '18:30' , '19:00' , '19:30' , 
	  '20:00' , '20:30' , '21:00' );
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Office Manager Mode
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
if ($accessOfficeMgr == 1) {
	echo "    <div id=\"sidebarOfficeMgr\" class=\"sidebar\">\n";
  if (is_numeric(intVal($_GET[week]))) {
	  $weekLeft = intVal($_GET[week]) - 1;
	  $weekRight = intVal($_GET[week]) + 1;
	  $offset = intVal($_GET[week]) * 7 + 1; 
  } else {
	  $weekLeft = -1;
	  $weekRight = 1;
	  $offset = 1;	  
  }
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?week=".$weekLeft."\"><-- ".$fieldNames[week]."</a></span>\n";
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?week=".$weekRight."\">".$fieldNames[week]."--></a></span>\n";
	echo "      <h3>".$fieldNames[baseCalendar]."</h3>\n";
	
	
	//Pull Clinic Calendar from masterCalendar Table
	$query = "SELECT *  FROM `".$moduleDB."`.`masterCalendar` WHERE `providerId` = 'clinic'";
	$result = $mysqli->query($query);
	$row = $result->fetch_object();
	
  foreach ($dayArray as $key=>$value) {
	$check = $key."Open";	  
    echo "      <h3>".$value."</h3>\n";
	echo "      <select id=\"".$check."\" onChange=\"calendarBaseUpdate('".$check."', 'clinic');\">\n";
	if ($row->$check == "00:00:00")  { echo "        <option select=\"selected\"></option>\n"; }
	foreach ($hoursArray as $hour) {
      echo "        <option ";
	  if ($row->$check == $hour.":00") { echo "selected=\"selected\" "; }
	    echo " >".$hour."</option>\n";
	}
    echo "      </select>\n";
	$check = $key."Close";	  
	echo "      <select id=\"".$check."\" onChange=\"calendarBaseUpdate('".$check."' , 'clinic');\">\n";
	if ($row->$check == "00:00:00")  { echo "        <option select=\"selected\" value=\"00:00:00\"></option>\n"; }
	foreach ($hoursArray as $hour) {
      echo "        <option ";
	  if ($row->$check == $hour.":00") { echo "selected=\"selected\" "; }
	    echo " >".$hour."</option>\n";
	}
    echo "      </select>\n";
   }
  
  echo "    </div>\n"; //end div sidebar
	
  /////////////////////////////Calendar
  //Get first day of week:
  $day = date('w');
  $weekStart = date('m-d-Y', strtotime('-'.$day.'+'.$offset.' days'));
	
	
  echo "    <div id=\"calendar\">\n";
  echo "      <table>\n";
  echo "        <tr>\n";
  $offsetWeekday = 0;
  foreach ($dayArray as $key=>$value) {
    $start = $key."Open";	
    $stop = $key."Close";
	$weekDate = date('Y-m-d', strtotime('-'.$day + $offset + $offsetWeekday.' days')); //weekdate
	//Pull Provider's calendar:
	$queryCalendar = "SELECT *  FROM `".$moduleDB."`.`calendarClinic` WHERE `date` = '".$weekDate."'";
	$resultCalendar = $mysqli->query($queryCalendar);
	if ($resultCalendar->num_rows >= 1) {
	  while ( $rowCalendar = $resultCalendar->fetch_object() ) {
		$daySchedule = $rowCalendar;
	  }
	} else { //If no calendar entry, set default values
		$daySchedule = array( '05:30' => 0 , '06:00' => 0 , '06:30' => 0 , '07:00' => 0 , '07:30' => 0 , '08:00' => 0 , '08:30' => 0 , '09:00' => 0 , '09:30' => 0 , 
							 '10:00' => 0 , '10:30' => 0 , '11:00' => 0 , '11:30' => 0 , '12:00' => 0 , '12:30' => 0 , '13:00' => 0 , '13:30' => 0 , '14:00' => 0 , '14:30' => 0 , 
							 '15:00' => 0 , '15:30' => 0 , '16:00' => 0 , '16:30' => 0 , '17:00' => 0 , '17:30' => 0 , '18:00' => 0 , '18:30' => 0 , '19:00' => 0 , '19:30' => 0 , 
							 '20:00' => 0 , '20:30' => 0 , '21:00' => 0 , ); 
	}
	$resultCalendar->close();
	
    echo "          <td>\n           <table>\n";
	echo "              <tr><th style=\"width:10em;\">".$value."<br />".$weekDate."</th></tr>\n";
 	foreach ($hoursArray as $hour) {
	  $check = $hour.":00";
      echo "            <tr>";
		  $class = "";
		
		  //Flag cells that are before clinic's base hours
	  	  if ($check < $row->$start || $check > $row->$stop) {
			  $class = "calendarNotAvailable";
		  }
		  //Flag cells that are specifically marked on the clinic's calendar
          if ($daySchedule->$hour == 2) {
			  $class = "calendarDefault";
		  }	
		  if ($daySchedule->$hour == 1) {
			  $class = "calendarNotAvailable";
		  }	
		  if (strlen($daySchedule->$hour) > 1) { //e.g. if hour other than 0 (default) or 1 (notAvailable) or 2(override base availability)
			  $class = "calendarApptScheduled";
			  $patient = ": ".$daySchedule->$hour;
		  }	else {
			  $patient = ""; //reset to no patient
		  }
 	
	  if (strlen($class) == 0) { $class = "calendarDefault"; } // Set Default Class
	  echo "<td class=\"".$class."\" id=\"tdcalendar".$key.$hour."\" name=\"tdcalendar".$key.$hour."\"  ><span id=\"calendar".$key.$hour."\" name=\"calendar".$key.$hour."\" 
	    onclick=\"calendarMasterUpdate('".$weekDate."', '".$hour."' , 'calendar".$key.$hour."' , '".trim($class)."');\">".$hour.$patient;
	  echo "</span></td></tr>\n";
	}	 
    echo "            </table>\n         </td>\n";
	   $offsetWeekday++; //increment day
  }
  echo "        </tr>\n";
  echo "      </table>\n";
  echo "    </div>\n"; //end div calendar
  $result->close;
  } //end Office Manager Calendar
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Provider Mode
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($accessProvider == 1) {
	echo "    <div id=\"sidebarProvider\" class=\"sidebar\">\n";
	  if (is_numeric(intVal($_GET[week]))) {
	  $weekLeft = intVal($_GET[week]) - 1;
	  $weekRight = intVal($_GET[week]) + 1;
	  $offset = intVal($_GET[week]) * 7 + 1; 
  } else {
	  $weekLeft = -1;
	  $weekRight = 1;
	  $offset = 1;	  
  }
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?week=".$weekLeft."\"><-- ".$fieldNames[week]."</a></span>\n";
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?week=".$weekRight."\">".$fieldNames[week]."--></a></span>\n";
	echo "      <h3>".$fieldNames[baseCalendar]."</h3>\n";
	
	//Pull Clinic Calendar from masterCalendar Table
	$query = "SELECT *  FROM `".$moduleDB."`.`masterCalendar` WHERE `providerId` = 'clinic'";
	$result = $mysqli->query($query);
	while ( $row = $result->fetch_object() ) {
		$clinicHours = $row;
	}
	$result->close();
	
	//Pull Base Calendar from masterCalendar Table
	$query = "SELECT *  FROM `".$moduleDB."`.`masterCalendar` WHERE `providerId` = '".$_SESSION['amomsId']."'";
	$result = $mysqli->query($query);
    $row = $result->fetch_object(); 
	
  foreach ($dayArray as $key=>$value) {
	$check = $key."Open";	  
    echo "      <h3>".$value."</h3>\n";
	echo "      <select id=\"".$check."\" onChange=\"calendarBaseUpdate('".$check."' , '".$_SESSION['amomsId']."');\">\n";
	if ($row->$check == "00:00:00")  { echo "        <option select=\"selected\"></option>\n"; }
	foreach ($hoursArray as $hour) {
      echo "        <option ";
	  if ($row->$check == $hour.":00") { echo "selected=\"selected\" "; }
	    echo " >".$hour."</option>\n";
	}
    echo "      </select>\n";
	$check = $key."Close";	  
	echo "      <select id=\"".$check."\" onChange=\"calendarBaseUpdate('".$check."' , '".$_SESSION['amomsId']."');\">\n";
	if ($row->$check == "00:00:00")  { echo "        <option select=\"selected\" value=\"00:00:00\"></option>\n"; }
	foreach ($hoursArray as $hour) {
      echo "        <option ";
	  if ($row->$check == $hour.":00") { echo "selected=\"selected\" "; }
	    echo " >".$hour."</option>\n";
	}
    echo "      </select>\n";
   }
  
  echo "    </div>\n"; //end div sidebar
	
  /////////////////////////////Calendar	
  //Get first day of week:
  $day = date('w');
  $weekStart = date('m-d-Y', strtotime('-'.$day.'+'.$offset.' days'));
	
	
  echo "    <div id=\"calendar\">\n";
  echo "      <table>\n";
  echo "        <tr>\n";
  $offsetWeekday = 0;
  foreach ($dayArray as $key=>$value) {
    $start = $key."Open";	
    $stop = $key."Close";
	$weekDate = date('Y-m-d', strtotime('-'.$day + $offset + $offsetWeekday.' days')); //weekdate
	//Pull Provider's calendar:
	$queryCalendar = "SELECT *  FROM `".$moduleDB."`.`calendar".$_SESSION['amomsId']."` WHERE `date` = '".$weekDate."'";
	$resultCalendar = $mysqli->query($queryCalendar);
	if ($resultCalendar->num_rows >= 1) {
	  while ( $rowCalendar = $resultCalendar->fetch_object() ) {
		$daySchedule = $rowCalendar;
	  }
	} else { //If no calendar entry, set default values
		$daySchedule = array( '05:30' => 0 , '06:00' => 0 , '06:30' => 0 , '07:00' => 0 , '07:30' => 0 , '08:00' => 0 , '08:30' => 0 , '09:00' => 0 , '09:30' => 0 , 
							 '10:00' => 0 , '10:30' => 0 , '11:00' => 0 , '11:30' => 0 , '12:00' => 0 , '12:30' => 0 , '13:00' => 0 , '13:30' => 0 , '14:00' => 0 , '14:30' => 0 , 
							 '15:00' => 0 , '15:30' => 0 , '16:00' => 0 , '16:30' => 0 , '17:00' => 0 , '17:30' => 0 , '18:00' => 0 , '18:30' => 0 , '19:00' => 0 , '19:30' => 0 , 
							 '20:00' => 0 , '20:30' => 0 , '21:00' => 0 , ); 
	}
	$resultCalendar->close();
	//Pull Clinic Calendar
	$queryCalendar = "SELECT *  FROM `".$moduleDB."`.`calendarClinic` WHERE `date` = '".$weekDate."'";
	$resultCalendar = $mysqli->query($queryCalendar);
	if ($resultCalendar->num_rows >= 1) {
	  while ( $rowCalendar = $resultCalendar->fetch_object() ) {
		$dayClinicSchedule = $rowCalendar;
	  }
	} else { //If no calendar entry, set default values
		$dayClinicSchedule = array( '05:30' => 0 , '06:00' => 0 , '06:30' => 0 , '07:00' => 0 , '07:30' => 0 , '08:00' => 0 , '08:30' => 0 , '09:00' => 0 , '09:30' => 0 , 
							 '10:00' => 0 , '10:30' => 0 , '11:00' => 0 , '11:30' => 0 , '12:00' => 0 , '12:30' => 0 , '13:00' => 0 , '13:30' => 0 , '14:00' => 0 , '14:30' => 0 , 
							 '15:00' => 0 , '15:30' => 0 , '16:00' => 0 , '16:30' => 0 , '17:00' => 0 , '17:30' => 0 , '18:00' => 0 , '18:30' => 0 , '19:00' => 0 , '19:30' => 0 , 
							 '20:00' => 0 , '20:30' => 0 , '21:00' => 0 , ); 
	}
	$resultCalendar->close();	
	  
    echo "          <td>\n           <table>\n";
	echo "              <tr><th style=\"width:10em;\">".$value."<br />".$weekDate."</th></tr>\n";
 	foreach ($hoursArray as $hour) {
	  $check = $hour.":00";
      echo "            <tr>";
		  //checksum
		  $class = "";
		  //Flag cells that are before or after Provider's base hours
	  	  if ($check < $row->$start || $check > $row->$stop ) {
			  $class = "calendarNotAvailable";
		  }
		  //Flag cells that are specifically marked on the Provider's calendar
          if ($daySchedule->$hour == 2) {
			  $class = "calendarDefault";
		  }	
		
		  if ($daySchedule->$hour == 1 || substr($daySchedule->$hour,0,1) == 1) {
			  $class = "calendarNotAvailable";
		  }	
		  if (strlen($daySchedule->$hour) > 1 && substr($daySchedule->$hour,0,1) != 1) { //e.g. if hour other than 0 (default) or 1 (notAvailable) or 2(override base availability)
			  $class = "calendarApptScheduled";
			  $patient = ": ".$daySchedule->$hour;
		  }	else {
			  $patient = ""; //reset to no patient
		  }
 		  //Hide cells before / after clinic hours except closed exceptions ($dayClinicSchedule->$hour == 1 and open exceptions $dayClinicSchedule->$hour == 2
	  	  if (($check < $clinicHours->$start || $check > $clinicHours->$stop || $dayClinicSchedule->$hour == 1) && $dayClinicSchedule->$hour != 2) {
			  $class = "calendarTimeHide";
		  } 
	  if (strlen($class) == 0) { $class = "calendarDefault"; } // Set Default Class
	  echo "<td class=\"".$class."\" id=\"tdcalendar".$key.$hour."\" name=\"tdcalendar".$key.$hour."\"  ><span id=\"calendar".$key.$hour."\" name=\"calendar".$key.$hour."\" 
	    onclick=\"calendarAvailabilityUpdate('".$weekDate."', '".$hour."' , 'calendar".$key.$hour."' , '".trim($class)."');\">".$hour.$patient;
	  echo "</span></td></tr>\n";
	}	 
    echo "            </table>\n         </td>\n";
	   $offsetWeekday++; //increment day
  }
  echo "        </tr>\n";
  echo "      </table>\n";
  echo "    </div>\n"; //end div calendar
  $result->close;
  } //end Provider Calendar
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Receptionist Mode
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
if ($accessReceptionist == 1) {
	
    //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 
  if ($_GET['provider']) {
    $cleanProvider = htmlentities($_GET['provider'], ENT_COMPAT, 'UTF-8');
	$mysqlProvider = $mysqli->real_escape_string($cleanProvider);	
  }
	
	echo "    <div id=\"sidebarReceptionist\" class=\"sidebar\">\n";
  if (is_numeric(intVal($_GET[week]))) {
	  $weekLeft = intVal($_GET[week]) - 1;
	  $weekRight = intVal($_GET[week]) + 1;
	  $offset = intVal($_GET[week]) * 7 + 1; 
  } else {
	  $weekLeft = -1;
	  $weekRight = 1;
	  $offset = 1;	  
  }
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?pid=".$patientId."&week=".$weekLeft."&provider=".$mysqlProvider."\"><-- ".$fieldNames[week]."</a></span>\n";
	echo "      <span style=\"width:35%;\" class=\"button\"><a class=\"buttonlink\" href=\"calendar.php?pid=".$patientId."&week=".$weekRight."&provider=".$mysqlProvider."\">".$fieldNames[week]."--></a></span>\n";
  //Select Provider
	echo "  <p><label for=\"selectProvider\">".$fieldNames[selectProvider]."</label><select id=\"selectProvider\" name=\"selectProvider\" 
	   onChange=\"window.open(this.options[ this.selectedIndex ].value, '_self')\" >\n";
	echo "    <option></option>\n";
	$queryProvider = "SELECT CONCAT(`nameFirst`,' ',`nameLast`) AS `name`, `userId` AS `providerId` FROM `".$moduleDB."`.`user` WHERE `provider` = 1";
	$resultProvider = $mysqli->query($queryProvider);
	while ($rowProvider = $resultProvider->fetch_object()) {
		echo "    <option value=\"calendar.php?pid=".$patientId."&week=".$_GET[week]."&provider=".$rowProvider->providerId."\">".$rowProvider->name."</option>\n";
	}
	echo "  </select></p>\n";
  	$resultProvider->close;
	
	//Select Treatment
	echo "  <p><label for=\"selectTreatment\">".$fieldNames[selectTreatment]."</label><select id=\"selectTreatment\" name=\"selectTreatment\" 
	   onChange=\"calendarSelectTreatment('selectTreatment')\" >\n";
	echo "    <option></option>\n";
	$queryTreatment = "SELECT `treatmentCode` , `treatment` FROM `".$moduleDB."`.`treatmentCode`";
	$resultTreatment = $mysqli->query($queryTreatment);
	while ($rowTreatment = $resultTreatment->fetch_object()) {
		echo "    <option value=\"".$rowTreatment->treatmentCode."\">".$rowTreatment->treatment."</option>\n";
	}
	echo "  </select></p>\n";
  	$resultTreatment->close;	
	
	echo "<p>".$fieldNames[treatmentDuration].": <b><span id=\"treatment\" name=\"treatment\"></span></b> ".$fieldNames[minutes]."</p>\n";
  
  echo "    </div>\n"; //end div sidebar
	
  /////////////////////////////Calendar
  //Get first day of week:
  $day = date('w');
  $weekStart = date('m-d-Y', strtotime('-'.$day.'+'.$offset.' days'));
	
  	//Pull Clinic Calendar from masterCalendar Table
	$query = "SELECT *  FROM `".$moduleDB."`.`masterCalendar` WHERE `providerId` = 'clinic'";
	$result = $mysqli->query($query);
	while ( $row = $result->fetch_object() ) {
		$clinicHours = $row;
	}
	$result->close();	
	
	
  echo "    <div id=\"calendar\">\n";
if ($_GET['provider']) {
  echo "<h3>".$mysqlProvider."</h3>\n";
	
  	//Pull Base Provider Calendar from masterCalendar Table
	$query = "SELECT *  FROM `".$moduleDB."`.`masterCalendar` WHERE `providerId` = '".$mysqlProvider."'";
	$result = $mysqli->query($query);
    $row = $result->fetch_object(); 	
	
  echo "      <table>\n";
  echo "        <tr>\n";
  $offsetWeekday = 0;
  foreach ($dayArray as $key=>$value) {
    $start = $key."Open";	
    $stop = $key."Close";
	$weekDate = date('Y-m-d', strtotime('-'.$day + $offset + $offsetWeekday.' days')); //weekdate
	//Pull Provider's Full calendar:
	$queryCalendar = "SELECT *  FROM `".$moduleDB."`.`calendar".$mysqlProvider."` WHERE `date` = '".$weekDate."'";
	$resultCalendar = $mysqli->query($queryCalendar);
	if ($resultCalendar->num_rows >= 1) {
	  while ( $rowCalendar = $resultCalendar->fetch_object() ) {
		$daySchedule = $rowCalendar;
	  }
	} else { //If no calendar entry, set default values
		$daySchedule = array( '05:30' => 0 , '06:00' => 0 , '06:30' => 0 , '07:00' => 0 , '07:30' => 0 , '08:00' => 0 , '08:30' => 0 , '09:00' => 0 , '09:30' => 0 , 
							 '10:00' => 0 , '10:30' => 0 , '11:00' => 0 , '11:30' => 0 , '12:00' => 0 , '12:30' => 0 , '13:00' => 0 , '13:30' => 0 , '14:00' => 0 , '14:30' => 0 , 
							 '15:00' => 0 , '15:30' => 0 , '16:00' => 0 , '16:30' => 0 , '17:00' => 0 , '17:30' => 0 , '18:00' => 0 , '18:30' => 0 , '19:00' => 0 , '19:30' => 0 , 
							 '20:00' => 0 , '20:30' => 0 , '21:00' => 0 , ); 
	}
	$resultCalendar->close();
	
	//Pull Clinic Calendar
	$queryCalendar = "SELECT *  FROM `".$moduleDB."`.`calendarClinic` WHERE `date` = '".$weekDate."'";
	$resultCalendar = $mysqli->query($queryCalendar);
	if ($resultCalendar->num_rows >= 1) {
	  while ( $rowCalendar = $resultCalendar->fetch_object() ) {
		$dayClinicSchedule = $rowCalendar;
	  }
	} else { //If no calendar entry, set default values
		$dayClinicSchedule = array( '05:30' => 0 , '06:00' => 0 , '06:30' => 0 , '07:00' => 0 , '07:30' => 0 , '08:00' => 0 , '08:30' => 0 , '09:00' => 0 , '09:30' => 0 , 
							 '10:00' => 0 , '10:30' => 0 , '11:00' => 0 , '11:30' => 0 , '12:00' => 0 , '12:30' => 0 , '13:00' => 0 , '13:30' => 0 , '14:00' => 0 , '14:30' => 0 , 
							 '15:00' => 0 , '15:30' => 0 , '16:00' => 0 , '16:30' => 0 , '17:00' => 0 , '17:30' => 0 , '18:00' => 0 , '18:30' => 0 , '19:00' => 0 , '19:30' => 0 , 
							 '20:00' => 0 , '20:30' => 0 , '21:00' => 0 , ); 
	}
	$resultCalendar->close();	
	  
    echo "          <td>\n           <table>\n";
	echo "              <tr><th style=\"width:10em;\">".$value."<br />".$weekDate."</th></tr>\n";
 	foreach ($hoursArray as $hour) {
	  $check = $hour.":00";
      echo "            <tr>";
		  //Hide cells before / after clinic hours except closed exceptions ($dayClinicSchedule->$hour == 1 and open exceptions $dayClinicSchedule->$hour == 2
	  	  if (($check < $clinicHours->$start || $check > $clinicHours->$stop || $dayClinicSchedule->$hour == 1 || $daySchedule->$hour == 1 || $check < $row->$start || $check > $row->$stop) && $daySchedule->$hour != 2) {
			  $class = "calendarTimeHide";
		  } else {
			  $class = "";
		  } 
		  if (strlen($daySchedule->$hour) > 1) { //e.g. if hour other than 0 (default) or 1 (notAvailable) or 2(override base availability)
			  $class = "calendarApptScheduled";
			  $patient = ": ".substr($daySchedule->$hour,1);
		  }	else {
			  $patient = ""; //reset to no patient
		  }
 	
	  if (strlen($class) == 0) { $class = "calendarDefault"; } // Set Default Class
	    echo "<td class=\"".$class."\" id=\"tdcalendar".$key.$hour."\" name=\"tdcalendar".$key.$hour."\"  >\n";
		if( $class != "calendarTimeHide") {
			echo "<span id=\"calendar".$key.$hour."\" name=\"calendar".$key.$hour."\" 
	   			 onclick=\"calendarSchedulePatient('".$weekDate."', '".$hour."' , 'calendar".$key.$hour."' , '".trim($class)."' , '".$patientId."' , 'treatment' , '".$mysqlProvider."');\">".$hour.$patient."</span>\n";
	    } else {
		    echo "<span id=\"calendar".$key.$hour."\" name=\"calendar".$key.$hour."\">".$hour.$patient."</span>\n";
		}
	  echo "</td></tr>\n";
	}	 
    echo "            </table>\n         </td>\n";
	   $offsetWeekday++; //increment day
  }
  echo "        </tr>\n";
  echo "      </table>\n";
} //endif $_GET[provider]
  echo "    </div>\n"; //end div calendar
  $result->close;
  } //end ReceptionistCalendar
} //end Authorized Access
include "footer.php";
?>
