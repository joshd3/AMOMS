<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Medical Appointment/Cita Medica</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('appointment').className = 'active';
  };
</script>

<?php
 include "header.php";
 include "include/includeTranslation.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessProvider == 0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de Cita Medico. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Medical Appointment Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 	
	
  //Create an array of diagnostic codes and values
  $query = "SELECT * FROM `".$moduleDB."`.`diagnosticCode`";
  $result = $mysqli->query($query);
  while ($row = $result->fetch_object()) {
    $diagnosticCode[$row->diagnosticCode] = $row->diagnosis;
  }	
  $result->close();	
  //Create an array of diagnostic codes and values
  $query = "SELECT * FROM `".$moduleDB."`.`treatmentCode`";
  $result = $mysqli->query($query);
  while ($row = $result->fetch_object()) {
    $treatmentCode[$row->treatmentCode] = $row->treatment;
  }	
  $result->close();	
	
	
?>
  <form method="post" id="newAppt" action="appointment.php?pid=<?php echo $mySQLiPatientId; ?>">
	  <input type="submit" value="<?php echo $fieldNames[newAppt]; ?>" name="newAppt" id="newAppt" />
  </form>
	
<?php
	
  $query = "SELECT `allergy` FROM `".$moduleDB."`.`patientmedicalhistory` WHERE `patientId` = '".$mySQLiPatientId."'";
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
	
  echo "<p>".$fieldNames[allergies].": ".$row[allergy]."</p>\n";
  $result->close();
  
  if($_POST['newAppt']) {
    $query = "INSERT INTO `".$moduleDB."`.`patientvisit` (`appointmentDate` , `patientId` , `createDate`) VALUES (CURDATE(),  '".$mySQLiPatientId."', CURDATE()) ";
    $result = $mysqli->query($query);	  
	echo "<meta http-equiv=\"refresh\" content=\"10; URL='http://lwcsurvey.gatech.edu/amoms/appointment.php?pid=".$mySQLiPatientId."'\" />"; 
  }
  echo "    <table>\n";	
  $query = "SELECT * FROM `".$moduleDB."`.`patientvisit` WHERE `patientId` = '".$mySQLiPatientId."' AND `createDate` >=  ( CURDATE() - INTERVAL 7 DAY ) ORDER BY `appointmentDate` DESC";
  $result = $mysqli->query($query);
	
    $weeksArray = range (0,12);
	
    while ($row = $result->fetch_object()) {
      echo "      <tr>\n        <th>".$fieldNames[apptDate]."</th>\n       <th>".$fieldNames[bloodPressure]."</th>
	  \n       <th>".$fieldNames[pulse]."</th>\n       <th>".$fieldNames[respiration]."</th>
	  \n       <th>".$fieldNames[height]."</th>\n       <th>".$fieldNames[weight]."<br />".$fieldNames[nextAppt]."</th>\n     </tr>\n";
		
      echo "      <tr><td><input value=\"".$row->appointmentDate."\" onblur=\"updateAppointment('".$patientId."', 'appointmentDate', '".$row->appointmentId."');\" 
	  id=\"appointmentDate".$row->appointmentId."\" name=\"appointmentDate".$row->appointmentId."\" /></td>\n";
      echo "        <td><input value=\"".$row->bloodPressure."\" onblur=\"updateAppointment('".$patientId."', 'bloodPressure', '".$row->appointmentId."');\" 
	  id=\"bloodPressure".$row->appointmentId."\" name=\"bloodPressure".$row->appointmentId."\" /></td>\n";
      echo "        <td><input value=\"".$row->pulse."\" onblur=\"updateAppointment('".$patientId."', 'pulse', '".$row->appointmentId."');\" 
	  id=\"pulse".$row->appointmentId."\" name=\"pulse".$row->appointmentId."\" /></td>\n";
      echo "        <td><input value=\"".$row->respiration."\" onblur=\"updateAppointment('".$patientId."', 'respiration', '".$row->appointmentId."');\" 
	  id=\"respiration".$row->appointmentId."\" name=\"respiration".$row->appointmentId."\" /></td>\n";
      echo "        <td><input value=\"".$row->height."\" onblur=\"updateAppointment('".$patientId."', 'height', '".$row->appointmentId."');\" 
	  id=\"height".$row->appointmentId."\" name=\"height".$row->appointmentId."\" /></td>\n";
	  echo "        <td><input value=\"".$row->weight."\" onblur=\"updateAppointment('".$patientId."', 'weight', '".$row->appointmentId."');\" 
	  id=\"weight".$row->appointmentId."\" name=\"weight".$row->appointmentId."\" /></td>\n";
      echo "      </tr>\n";
		
	  echo "      <tr><td>".$fieldNames[reason]."</td><td colspan=4><input size=80 value=\"".$row->reasonForVisit."\" onblur=\"updateAppointment('".$patientId."', 'reasonForVisit', '".$row->appointmentId."');\" 
	  id=\"reasonForVisit".$row->appointmentId."\" name=\"reasonForVisit".$row->appointmentId."\" /></td>\n";
	  echo "        <td><input title=\"".$fieldNames[followup]."\" value=\"".$row->followup."\" onblur=\"updateAppointment('".$patientId."', 'followup', '".$row->appointmentId."');\" 
	  id=\"followup".$row->appointmentId."\" name=\"followup".$row->appointmentId."\" /></td>\n";
      echo "      </tr>\n";
	
	  //Diagnostic Code Drop-Down
	  echo "      <tr><td>".$fieldNames[diagnosis]."</td>\n";
      echo "        <td>\n";
      echo "          <select name=\"diagnosticCode".$row->appointmentId."\" id=\"diagnosticCode".$row->appointmentId."\" 
	                  onchange=\"updateAppointmentSelect('".$patientId."', 'diagnosticCode' , '".$row->appointmentId."' , 'diagnosticCodeView".$row->appointmentId."');\">\n";
      echo "            <option selected=\"selected\" value=\"".$row->diagnosticCode."\">".$diagnosticCode[$row->diagnosticCode]."</option>\n";
      foreach ($diagnosticCode as $key=>$value) {
        echo "            <option title=\"".$value."\">".$key."</option>\n";
      }
      echo "          </select>\n";
      echo "        </td>\n";
	  echo "      <td colspan=4><span id=\"diagnosticCodeView".$row->appointmentId."\">".$diagnosticCode[$row->diagnosticCode]."</span></td></tr>\n";	
		
	  //Treatment Code Drop-Down
	  echo "      <tr><td>".$fieldNames[treatment]."</td>\n";
      echo "        <td>\n";
      echo "          <select name=\"treatmentCode".$row->appointmentId."\" id=\"treatmentCode".$row->appointmentId."\" 
	                  onchange=\"updateAppointmentSelect('".$patientId."', 'treatmentCode' , '".$row->appointmentId."' , 'treatmentCodeView".$row->appointmentId."');\">\n";
      echo "            <option selected=\"selected\" value=\"".$row->treatmentCode."\">".$treatmentCode[$row->treatmentCode]."</option>\n";
      foreach ($treatmentCode as $key=>$value) {
        echo "            <option title=\"".$value."\">".$key."</option>\n";
      }
      echo "          </select>\n";
      echo "        </td>\n";
	  echo "      <td colspan=4><span id=\"treatmentCodeView".$row->appointmentId."\">".$treatmentCode[$row->treatmentCode]."</span></td></tr>\n";
		
		  echo "      <tr><td>".$fieldNames[notes]."</td><td colspan=5><textarea rows=\"5\" cols=\"80\" onblur=\"updateAppointment('".$patientId."', 'notes', '".$row->appointmentId."');\" 
	  id=\"notes".$row->appointmentId."\" name=\"notes".$row->appointmentId."\">".$row->notes."</textarea></td>\n</tr>\n";	
	}
    $result->close();	
	
	
	
  
	  
  $query = "SELECT * FROM `".$moduleDB."`.`patientvisit` WHERE `patientId` = '".$mySQLiPatientId."' AND `createDate` <  ( CURDATE() - INTERVAL 7 DAY ) ORDER BY `appointmentDate` DESC";
  $result = $mysqli->query($query);
	
    while ($row = $result->fetch_object()) {
      echo "      <tr>\n        <th>".$fieldNames[apptDate]."</th>\n       <th>".$fieldNames[bloodPressure]."</th>
	  \n       <th>".$fieldNames[pulse]."</th>\n       <th>".$fieldNames[respiration]."</th>
	  \n       <th>".$fieldNames[height]."</th>\n       <th>".$fieldNames[weight]."</th>\n     </tr>\n";
      echo "      <tr><td>".$row->appointmentDate."</td><td>".$row->bloodPressure."</td><td>".$row->pulse."<td>".$row->respiration."</td><td>".$row->height."</td><td>".$row->weight."</td></tr>\n";
	  echo "      <tr><td>".$fieldNames[reason]."</td><td colspan=4>".$row->reasonForVisit."</td><td>".$row->followup."</td></tr>\n";
	  echo "      <tr><td>".$fieldNames[diagnosis]."</td><td>".$row->diagnosticCode."</td><td colspan=4>".$diagnosticCode[$row->diagnosticCode]."</td></tr>\n";	
	  echo "      <tr><td>".$fieldNames[treatment]."</td><td>".$row->treatmentCode."</td><td colspan=5>".$treatmentCode[$row->treatmentCode]."</td></tr>\n";	
	  echo "      <tr><td>".$fieldNames[notes]."</td><td colspan=5>".$row->notes."</td></tr>\n";
	}
    $result->close();	
	
echo "    </table>\n";	
} //end Authorized Access
include "footer.php";
?>
