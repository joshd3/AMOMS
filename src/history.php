<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Search</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('medicalHistory').className = 'active';
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
    echo "<h2>No tiene acceso al módulo de Historial Médico. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Medical History Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 
  
  $query = "SELECT * FROM `".$moduleDB."`.`patientmedicalhistory` WHERE `patientId` = '".$mySQLiPatientId."'";
  $result = $mysqli->query($query);
  if ($result->num_rows == 0) { // Add Patient to Database
    $result->close();
    $query = "INSERT INTO  `".$moduleDB."`.`patientmedicalhistory` (`patientId`) VALUES ( '".$mySQLiPatientId."' )";
    $result = $mysqli->query($query);
    $result->close();
    $query = "SELECT * FROM `".$moduleDB."`.`patientmedicalhistory` WHERE `patientId` = '".$mySQLiPatientId."'";
    $result = $mysqli->query($query);
  }
  $row = $result->fetch_array(MYSQLI_ASSOC);
	
	if ($languagePref == 'spa') {
      echo "  <p><label>Listar alergias::&nbsp;&nbsp;<input type=\"text\" id=\"allergy\" name=\"allergy\" value=\"".$row[allergy]."\"       onblur=\"updateHistory('".$mySQLiPatientId."', 'allergy');\"/></label></p>\n";
      echo "  <table>\n    <tr>\n      <th>Condición</th>\n      <th>Yo</th>\n      <th>Madre</th>\n      <th>Padre</th>\n      <th>Hermano</th>\n      <th>Niño</th>\n    </tr>\n";
	} else {
	  echo "  <p><label>List Allergies:&nbsp;&nbsp;<input type=\"text\" id=\"allergy\" name=\"allergy\" value=\"".$row[allergy]."\"       onblur=\"updateHistory('".$mySQLiPatientId."', 'allergy');\"/></label></p>\n";
      echo "  <table>\n    <tr>\n      <th>Condition</th>\n      <th>Self</th>\n      <th>Mother</th>\n      <th>Father</th>\n      <th>Sibling</th>\n      <th>Child</th>\n    </tr>\n";
	}
    foreach ($row AS $key=>$value) {
      if ($key == 'allergy' || $key =='patientId' || $key =='id') { // do nothing
      } else  {
        echo "    <tr>\n      <td>".ucwords(str_replace('_', ' ', $key))."</td>\n";
        if ($value == 0) { 
           echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."1\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1);\"/>\n    </td>\n";
           echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."10\" id=\"".$key."10\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10);\"/>\n    </td>\n";
           echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."100\" id=\"".$key."100\" onclick=\"updateHistoryBox'".$mySQLiPatientId."',('".$key."', 100);\"/>\n    </td>\n";
           echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1000\" id=\"".$key."1000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1000);\"/>\n    </td>\n";
           echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."10000\" id=\"".$key."10000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10000);\"/>\n    </td>\n";
       } else {
           if (substr($value, -1) == 1) {
             echo "      <td><input type=\"checkbox\" checked=\"checked\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."1\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1);\"/>\n    </td>\n";
           } else {
             echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."1\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1);\"/>\n    </td>\n";
           } 
           if (substr($value, -2, 1) == 1) {
             echo "      <td><input type=\"checkbox\" checked=\"checked\" title=\"".$key."\" name=\"".$key."10\" id=\"".$key."10\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10);\"/>\n    </td>\n";
           } else {
             echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."10\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10);\"/>\n    </td>\n";
           } 
           if (substr($value, -3, 1) == 1) {
             echo "      <td><input type=\"checkbox\" checked=\"checked\" title=\"".$key."\" name=\"".$key."100\" id=\"".$key."100\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 100);\"/>\n    </td>\n";
           } else {
             echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."100\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 100);\"/>\n    </td>\n";
           } 
           if (substr($value, -4, 1) == 1) {
             echo "      <td><input type=\"checkbox\" checked=\"checked\" title=\"".$key."\" name=\"".$key."1000\" id=\"".$key."1000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1000);\"/>\n    </td>\n";
           } else {
             echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."1000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 1000);\"/>\n    </td>\n";
           } 
           if (substr($value, -5, 1) == 1) {
             echo "      <td><input type=\"checkbox\" checked=\"checked\" title=\"".$key."\" name=\"".$key."10000\" id=\"".$key."10000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10000);\"/>\n    </td>\n";
           } else {
             echo "      <td><input type=\"checkbox\" title=\"".$key."\" name=\"".$key."1\" id=\"".$key."10000\" onclick=\"updateHistoryBox('".$mySQLiPatientId."','".$key."', 10000);\"/>\n    </td>\n";
           } 
       }
        echo "    </tr>\n";
      }
    }
       $result->close();
  echo "  <table>\n";
  echo "  </div>\n"; //close Main Content Div
} //end Authorized Access
include "footer.php";
?>
