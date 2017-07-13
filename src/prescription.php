<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Prescription/Prescripción</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('prescription').className = 'active';
  };
</script>

<?php
 include "header.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessProvider == 0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de prescripción. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Prescription Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 
  if ($_POST['newSubmit']) {
     $cleanMedication = htmlentities($_POST['newMedication'], ENT_COMPAT, 'UTF-8');
     $cleanDosage = htmlentities($_POST['newDosage'], ENT_COMPAT, 'UTF-8');
     $cleanFrequency = htmlentities($_POST['newFrequency'], ENT_COMPAT, 'UTF-8');
     $cleanNote = htmlentities($_POST['newNote'], ENT_COMPAT, 'UTF-8');
     $cleanPatientId = htmlentities($_POST['newPatientId'], ENT_COMPAT, 'UTF-8');
	 $cleanProviderId = htmlentities($_SESSION['amomsId'], ENT_COMPAT, 'UTF-8');
	 $mysqlMedication = $mysqli->real_escape_string($cleanMedication);
	 $mysqlDosage = $mysqli->real_escape_string($cleanDosage);
	 $mysqlFrequency = $mysqli->real_escape_string($cleanFrequency);
	 if ($_POST['addGeneric'] == 'on') { $mysqlGeneric = 1; } else { $mysqlGeneric = 0; }
	 $mysqlNote = $mysqli->real_escape_string($cleanNote);
	 $mysqlPatientId = $mysqli->real_escape_string($cleanPatientId);
     $mysqlProviderId = $mysqli->real_escape_string($cleanProviderId);
     $query = "INSERT INTO `".$moduleDB."`.`patientprescription` (`patientId`, `providerId`, `medication`, `dosage`, `frequency`, `notes`, `current`, `dateStarted`, `generic`) 
	 VALUES ('".$mysqlPatientId."' , '".$mysqlProviderId."' ,'".$mysqlMedication."' ,'".$mysqlDosage."' ,'".$mysqlFrequency."' ,'".$mysqlNote."' , 1 , CURDATE() , ".$mysqlGeneric.")";
	  
	  echo $query;
      $result = $mysqli->query($query);
      //$result->close();
 
  }  	
	
  if ($_POST['addSubmit']) {
     $cleanMedication = htmlentities($_POST['addMedication'], ENT_COMPAT, 'UTF-8');
     $cleanDosage = htmlentities($_POST['addDosage'], ENT_COMPAT, 'UTF-8');
     $cleanFrequency = htmlentities($_POST['addFrequency'], ENT_COMPAT, 'UTF-8');
     $cleanNote = htmlentities($_POST['addNote'], ENT_COMPAT, 'UTF-8');
     $cleanPatientId = htmlentities($_POST['addPatientId'], ENT_COMPAT, 'UTF-8');
	 $mysqlMedication = $mysqli->real_escape_string($cleanMedication);
	 $mysqlDosage = $mysqli->real_escape_string($cleanDosage);
	 $mysqlFrequency = $mysqli->real_escape_string($cleanFrequency);
	 if ($_POST['addGeneric'] == 'on') { $mysqlGeneric = 1; } else { $mysqlGeneric = 0; }
	 $mysqlNote = $mysqli->real_escape_string($cleanNote);
	 $mysqlPatientId = $mysqli->real_escape_string($cleanPatientId);
     $query = "INSERT INTO `".$moduleDB."`.`patientprescription` (`patientId`, `providerId`, `medication`, `dosage`, `frequency`, `notes`, `current`, `dateStarted`, `generic`) 
	 VALUES ('".$mysqlPatientId."' , 'other' ,'".$mysqlMedication."' ,'".$mysqlDosage."' ,'".$mysqlFrequency."' ,'".$mysqlNote."' , 1 , CURDATE() , ".$mysqlGeneric.")";
	  
	  echo $query;
      $result = $mysqli->query($query);
      //$result->close();
 
  }  
  
	
  if ($languagePref == 'spa') {
    $fieldNames = array( 
		'medication' => 'Medicación' , 
		'dosage' => 'Dosificación' , 
		'frequency' => 'Frecuencia', 
		'notes' => 'Notas',
		'generic' => 'Genérico Bien' , 
		'genericTitle' => 'Haga clic aquí para permitir un sustituto genérico' ,
		'startDate' => 'Fecha de inicio' ,
		'endDate' => 'Fecha final',
		'printPage' => 'Impresa'
	); 
  } else {
    $fieldNames = array( 
		'medication' => 'Medication' , 
		'dosage' => 'Dosage' , 
		'frequency' => 'Frequency', 
		'notes' => 'Notes',
		'generic' => 'Generic Okay' , 
		'genericTitle' => 'Click here to allow for generic substitute',
		'startDate' => 'Start Date' ,
		'endDate' => 'End Date',
		'printPage' => 'Print'
	); 
  }
 ?>
  <form id="newRx" method="post" url="prescription.php">
	  
	<h3>New Prescription</h3>

	  <p>
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[medication]; ?>" name="newMedication" id="newMedication" />
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[dosage]; ?>" name="newDosage" id="newDosage" />
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[frequency]; ?>" name="newFrequency" id="newFrequency" />
		  <input type="checkbox" title="<?php echo $fieldNames[genericTitle]; ?>" name="newGeneric" id="newGeneric" /><?php echo $fieldNames[generic]; ?>
	  </p>
	  <p>
		  <input type="text" size="161" onClick="this.select();" value="<?php echo $fieldNames[notes]; ?>" name="addNote" id="addNote" />
		  <input type="submit" value="+" name="newSubmit" id="newSubmit" />
		  <input type="hidden" value="<?php echo $patientId ?>" name="newPatientId" value="newPatientId" />
	  </p>	  
	  
  <h3>Add Prescription from a non-AMOMS provider</h3>
	  
	  <p>
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[medication]; ?>" name="addMedication" id="addMedication" />
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[dosage]; ?>" name="addDosage" id="addDosage" />
		  <input type="text" size="50" onClick="this.select();" value="<?php echo $fieldNames[frequency]; ?>" name="addFrequency" id="addFrequency" />
		  <input type="checkbox" title="<?php echo $fieldNames[genericTitle]; ?>" name="addGeneric" id="addGeneric" /><?php echo $fieldNames[generic]; ?>
	  </p>
	  <p>
		  <input type="text" size="161" onClick="this.select();" value="<?php echo $fieldNames[notes]; ?>" name="addNote" id="addNote" />
		  <input type="submit" value="+" name="addSubmit" id="addSubmit" />
		  <input type="hidden" value="<?php echo $patientId ?>" name="addPatientId" value="addPatientId" />
	  </p>	 
	</form>

	
<?php
  echo "    <h3>Current Prescription(s)</h3>";
	
	
	 $query = "SELECT * FROM `".$moduleDB."`.`patientprescription` WHERE `patientId` = '".$patientId."' AND `dateEnded` = '0000-00-00';";
	 $result = $mysqli->query($query);
	 
	 while ($row = $result->fetch_object()) {
       echo "      <p>".$row->id."\n";
       echo "        <input type=\"text\" size=\"50\" onClick=\"this.select();\" onblur=\"updatePrescription('".$patientId."', 'medication', '".$row->id."');\" value=\"".$row->medication."\" name=\"medication".$row->id."\" id=\"medication".$row->id."\" />\n";
       echo "        <input type=\"text\" size=\"50\" onClick=\"this.select();\" onblur=\"updatePrescription('".$patientId."', 'dosage', '".$row->id."');\" value=\"".$row->dosage."\" name=\"dosage".$row->id."\" id=\"dosage".$row->id."\" />\n";
	   echo "        <input type=\"text\" size=\"50\" onClick=\"this.select();\" onblur=\"updatePrescription('".$patientId."', 'frequency', '".$row->id."');\" value=\"".$row->frequency."\" name=\"frequency".$row->id."\" id=\"frequency".$row->id."\" />\n";
	   if ($row->generic == 1) {
         echo "        <input type=\"checkbox\" checked=\"checked\" onclick=\"updatePrescriptionCheck('".$patientId."', 'generic', '".$row->id."');\" \title=\"Click here to allow for generic substitute\" name=\"generic".$row->id."\" id=\"generic".$row->id."\" />Generic Okay\n";
	   } else {
         echo "        <input type=\"checkbox\" onclick=\"updatePrescriptionCheck('".$patientId."', 'generic', '".$row->id."');\" title=\"Click here to allow for generic substitute\" name=\"generic".$row->id."\" id=\"generic".$row->id."\" />".$fieldNames[generic]."\n";
	   }
       echo "        &nbsp;".$row->providerId."&nbsp;\n";
       echo "      </p>\n";
       echo "      <p>\n";
	   if ($row->providerId != 'other') {
         echo "        <input type=\"submit\" value=\"".$fieldNames[printPage]."\" name=\"print".$row->id."\" id=\"print".$row->id."\" />\n";
	   } else {
         echo "        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	   }
       echo "        <input type=\"text\" size=\"160\" onClick=\"this.select();\" onblur=\"updatePrescription('".$patientId."', 'notes', '".$row->id."');\" value=\"".$row->notes."\" name=\"notes".$row->id."\" id=\"notes".$row->id."\" />\n";
       echo "        <input type=\"submit\" value=\"X\" title=\"Stop Prescription\" onclick=\"updatePrescriptionEnd('".$patientId."', ".$row->id.");\" name=\"stop".$row->id."\" id=\"stop".$row->id."\" />\n";
       echo "      </p>\n";
	 }
    $result->close();
  echo "    <h3>Former Prescription(s)</h3>";
    $query = "SELECT * FROM `".$moduleDB."`.`patientprescription` WHERE `patientId` = '".$patientId."' AND `dateEnded` != '0000-00-00' ORDER BY `dateEnded`,`dateStarted` DESC ;";
    $result = $mysqli->query($query);
	
    echo "    <table>\n      <tr>\n        <th>".$fieldNames[medication]."</th>\n        <th>".$fieldNames[dosage]."</th>\n        <th>".$fieldNames[frequency]."</th>\n        <th>".$fieldNames[startDate]."</th>\n        <th>".$fieldNames[endDate]."</th>\n        <th>".$fieldNames[notes]."</th>\n      </tr>\n";   	
    while ($row = $result->fetch_object()) {
      echo "      <tr><td>".$row->medication."</td><td>".$row->dosage."</td><td>".$row->frequency."</td><td>".$row->dateStarted."</td><td>".$row->dateEnded."</td><td>".$row->notes."</tr>\n";
	}
    $result->close();
    echo "    </table>\n";
	 
  echo "  </div>\n"; //close Main Content Div
} //end Authorized Access
include "footer.php";
?>
