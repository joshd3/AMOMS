<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Treatment List/Lista de tratamiento</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('treatment').className = 'active';
  };
</script>

<?php
 include "header.php";
 include "include/includeTranslation.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} elseif  ($accessOfficeMgr == 0) {
  if ($languagePref == 'spa') {
    echo "<h2>No tiene acceso al módulo de Lista de tratamiento. Póngase en contacto con el administrador de Office o administrador de sistema para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Treatment List Module.  Please contact the Office Manager or System Administrator for assistance.</h2>\n";
  }
} else { //begin Authorized Access
    //Control Sorting
	$sortOrder = "`treatmentCode` ASC"; //Default order
	$headerArray = array('treatmentCode', 'treatment', 'treatmentType' , 'duration' , 'charge');
    foreach ($headerArray AS $value) {
	  if ($_GET[sortby] == $value.'Asc') {
	    $sortOrder = "`".$value."` ASC";
	  } 
	  if ($_GET[sortby] == $value.'Desc') {
	    $sortOrder = "`".$value."` DESC";
      }
	}
	
	//Add New Treatment
	if ($_POST[addTreatment]) {
	  $defangArray = array('addTreatmentCode' , 'addTreatment' , 'addTreatmentType' , 'addTreatmentDuration' , 'addCharge');
	  foreach ($defangArray as $value) {
        ${'clean'.$value} = htmlentities($_POST[$value], ENT_COMPAT, 'UTF-8');
        ${'mysql'.ucwords($value)} = $mysqli->real_escape_string(${'clean'.$value});
	  }
	  //See how many similar usernames exist
		$query = "SELECT * FROM `".$moduleDB."`.`treatmentCode` WHERE `treatmentCode` = '".$mysqlAddTreatmentCode."'";
        $result = $mysqli->query($query);
		if ($result->num_rows > 0) { 
		  echo "<h2>".$fieldNames[treatmentExists]."</h2>"; 
          $result->close; 
	    } else {
		  $result->close; 
		//Add New Treatment
		  $query = "INSERT INTO `".$moduleDB."`.`treatmentCode` (`treatmentCode`, `treatment`, `treatmentType`, `duration`, `charge`) 
		    VALUES ('".$mysqlAddTreatmentCode."' , '".$mysqlAddTreatment."' , '".$mysqlAddTreatmentType."' , ".$mysqlAddTreatmentDuration." , ".$mysqlAddCharge.")";
			echo $query;
		  $result = $mysqli->query($query);
	    }
	}	
	
	echo "<h3>".$fieldNames[addTreatment]."</h3>\n";
	echo "<form method=\"post\" action=\"treatment.php\">\n";
	echo "<p>\n";
	echo "<label for=\"addTreatmentCode\">".$fieldNames[treatmentCode]."</label><input type=\"text\" id=\"addTreatmentCode\" name=\"addTreatmentCode\" />\n";
	echo "<label for=\"addTreatment\">".$fieldNames[treatment]."</label><input type=\"text\" id=\"addTreatment\" name=\"addTreatment\" />\n";
	echo "<label for=\"addTreatmentType\">".$fieldNames[treatmentType]."</label><input type=\"text\" id=\"addTreatmentType\" name=\"addTreatmentType\" />\n";
	echo "<label for=\"addTreatmentDuration\">".$fieldNames[treatmentDuration]."</label><input type=\"text\" id=\"addTreatmentDuration\" name=\"addTreatmentDuration\" />\n";
	echo "<label for=\"addCharge\">".$fieldNames[treatmentCharge]."</label><input type=\"text\" id=\"addCharge\" name=\"addCharge\" />\n";	
	echo "<input type=\"submit\" id=\"addTreatment\" name=\"addSubmit\" value=\"".$fieldNames[addTreatment]."\"/>\n";
	echo "</p>\n";	
	echo "</form>\n";	
    //Display existing treatments
	echo "<h3>".$fieldNames[manageTreatment]."</h3>\n";
	$query = "SELECT * FROM `".$moduleDB."`.`treatmentCode` ORDER BY ".$sortOrder;
    $result = $mysqli->query($query);
	
	echo "<table>\n";
	echo "  <tr>\n";
	echo "    <th>".$fieldNames[treatmentId]."</th>\n";
	
	//Set headers with sort functions
	foreach ($headerArray AS $value) {
	  if ($_GET[sortby] == $value.'Asc') {
	    echo "    <th><a href=\"treatment.php?sortby=".$value."Desc\">".$fieldNames[$value]." ▲</a></th>\n";
	  } elseif ($_GET[sortby] == $value.'Desc') {
	    echo "    <th><a href=\"treatment.php?sortby=".$value."Asc\">".$fieldNames[$value]." ▼</a></th>\n";
      } else {
	    echo "    <th><a href=\"treatment.php?sortby=".$value."Asc\">".$fieldNames[$value]." ▼</a></th>\n";
      }
	}
    echo "  </tr>\n";
    while ($row = $result->fetch_object()) {
	  echo "  <tr>\n"; 
	  echo "    <td>".$row->treatmentId."</td>\n";
      foreach ($headerArray AS $value) {
  	    echo "    <td><input id=\"".$value.$row->treatmentId."\" name=\"".$value.$row->treatmentId."\" value=\"".$row->$value."\"	
	    onblur=\"updateTreatment('".$row->treatmentId."', '".$value."');\"/></td>\n";
	  }		  
        echo "  </tr>\n";
	}
	echo "</table>\n";
	
	
} //end Authorized Access
include "footer.php";
?>
