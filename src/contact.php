<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Search</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('contactInfo').className = 'active';
  };
</script>

<?php
 include "header.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessReceptionist == 0 && $accessOfficeMgr ==0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de información de contacto. Póngase en contacto con el administrador de Office para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Contact Information Module.  Please contact the Office Manager for assistance.</h2>\n";
  }
} else { //begin Authorized Access
  echo "  <div id=\"mainContent\">\n";
  //If no patient ID, redirect to the search page
  if (strlen($patientId) == 1) { echo "<meta http-equiv=\"refresh\" content=\"0; URL='http://lwcsurvey.gatech.edu/amoms/search.php'\" />"; } 
  
  $query = "SELECT * FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$mySQLiPatientId."'";
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
   
  foreach ($row AS $key=>$value) {
    if ($key == 'patientId') { //don't make this field editable as it is the primary key
    } else {
      echo "<p><label>".strtoUpper($key)."&nbsp;&nbsp; <input type=\"text\" id=\"".$key."\" name=\"".$key."\" value=\"".$value."\"       onblur=\"updateContactField('".$mySQLiPatientId."', '".$key."');\"/></label></p>\n";
    }
  }
       $result->close();
  echo "  </div>\n"; //close Main Content Div
} //end Authorized Access
include "footer.php";
?>
