<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Authorization List/Lista de autorizaciones</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('userAdmin').className = 'active';
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
    echo "<h2>No tiene acceso al módulo de Lista de autorizaciones. Póngase en contacto con el administrador de Office o administrador de sistema para obtener ayuda.</h2>\n";
  } else {
    echo "<h2>You do not have access to the Authorization List Module.  Please contact the Office Manager or System Administrator for assistance.</h2>\n";
  }
} else { //begin Authorized Access
	//Add New User
	if ($_POST[addSubmit]) {
	  $defangArray = array('addNameLast' , 'addNameFirst' , 'addDOB' , 'addEmail');
	  foreach ($defangArray as $value) {
        ${'clean'.$value} = htmlentities($_POST[$value], ENT_COMPAT, 'UTF-8');
        ${'mysql'.ucwords($value)} = $mysqli->real_escape_string(${'clean'.$value});
	  }
	  //See how many similar usernames exist
		$mysqlUserId = substr($mysqlAddNameFirst,0,1).$mysqlAddNameLast.substr($mysqlAddDOB,0,4);
		$query = "SELECT * FROM `".$moduleDB."`.`user` WHERE `userID` = '".$mysqlUserId."'";
        $result = $mysqli->query($query);
		$distinctUser = $result->num_rows;
		if ($distinctUser == 0) {$distinctUser='';}
        $result->close;
		
		//Add New User
		$query = "INSERT INTO `".$moduleDB."`.`user` (`userId`, `nameLast`, `nameFirst`, `dob`, `email`, `dateAcctCreated`, `isActive`) 
		  VALUES ('".$mysqlUserId.$distinctUser."' , '".$mysqlAddNameLast."' , '".$mysqlAddNameFirst."' , '".$mysqlAddDOB."' , '".$mysqlAddEmail."' , CURDATE(), 1)";
		$result = $mysqli->query($query);
	}	
	
	
	echo "<h3>".$fieldNames[currentUsers]."</h3>\n";
    echo "<table>\n";
	echo "  <tr>\n";
	echo "    <th>".$fieldNames[userID]."</th>\n";
	echo "    <th>".$fieldNames[nameLast]."</th>\n";
	echo "    <th>".$fieldNames[nameFirst]."</th>\n";
	echo "    <th>".$fieldNames[email]."</th>\n";
	echo "    <th>".$fieldNames[DOB]."</th>\n";
	echo "    <th>".$fieldNames[receptionist]."</th>\n";
	echo "    <th>".$fieldNames[patientAcctMgr]."</th>\n";
	echo "    <th>".$fieldNames[officeMgr]."</th>\n";
	echo "    <th>".$fieldNames[provider]."</th>\n";
	echo "    <th>".$fieldNames[deaNumber]."</th>\n";
	echo "    <th>".$fieldNames[deactivate]."</th>\n";
	echo "    <th>".$fieldNames[resetPassword]."</th>\n";
	echo "  </tr>\n";
	
     $query = "SELECT * FROM `".$moduleDB."`.`user` WHERE `isActive` = 1 ORDER BY nameLast, nameFirst DESC";
     $result = $mysqli->query($query);
     while ($row = $result->fetch_object()) {
      echo "  <tr>\n";
		
	  //Userid
	  echo "    <td>\n";
	  echo $row->userId;
	  echo "    </td>\n";	
      //Input Boxes 
      $textInput = array('nameLast' , 'nameFirst'  ,  'email' , 'dob');
      foreach ($textInput as $textInput) {
        echo "    <td>\n      <input type=\"text\" name=\"".$row->userId."_".$textInput."\" id=\"".$row->userId."_".$textInput."\"  value=\"".$row->$textInput."\"  
		onblur=\"authListUpdate('".$row->userId."', '".$textInput."');\"/>\n           </td>\n";
      }
      //Check Boxes 
	  $checkInput = array('receptionist' , 'patientAcctMgr' , 'officeMgr' ,  'provider');
      foreach ($checkInput as $checkInput) {
	    if ($row->$checkInput == 1) {
          echo "      <td><input type=\"checkbox\"  name=\"".$row->userId."_".$checkInput."\" id=\"".$row->userId."_".$checkInput."\" checked=\"checked\"  
		      onclick=\"authListCheckbox('".$row->userId."', '".$checkInput."');\"/></td>\n";
        } else {
          echo "      <td><input type=\"checkbox\"  name=\"".$row->userId."_".$checkInput."\" id=\"".$row->userId."_".$checkInput."\" 
		    onclick=\"authListCheckbox('".$row->userId."', '".$checkInput."');\" /></td>\n";
        }		
	  }	 
		 
      //Provider Input Boxes 
	  if ($row->provider == 1) {	 
        $textInput = array('deaNumber');
        foreach ($textInput as $textInput) {
          echo "    <td>\n      <input type=\"text\" name=\"".$row->userId."_".$textInput."\" id=\"".$row->userId."_".$textInput."\"  value=\"".$row->$textInput."\"  
		  onblur=\"authListUpdate('".$row->userId."', '".$textInput."');\"/>\n           </td>\n";
        }
	  } else {
         echo "    <td style=\"visibility:hidden;\">&nbsp;</td>\n";
	  }	  
      //Buttons
      $submitInput = array('deactivate' , 'resetPassword' );
      foreach ($submitInput as $submitInput) {
        echo "    <td>\n      <input type=\"submit\" name=\"".$row->userId."_".$submitInput."\" id=\"".$row->userId."_".$submitInput."\"  value=\"".$fieldNames[$submitInput]."\"  
		onclick=\"authListButton('".$row->userId."', '".$submitInput."');\"/>\n           </td>\n";
      }			 
		 
      echo "  </tr>\n";		 
		
	} // end while ($row = $result->fetch_object())	
    $result->close;
    echo "</table>\n";
	
	echo "<h3>".$fieldNames[formerUsers]."</h3>\n";
    echo "<table>\n";
	echo "  <tr>\n";
	echo "    <th>".$fieldNames[userID]."</th>\n";
	echo "    <th>".$fieldNames[nameLast]."</th>\n";
	echo "    <th>".$fieldNames[nameFirst]."</th>\n";
	echo "    <th>".$fieldNames[email]."</th>\n";
	echo "    <th>".$fieldNames[DOB]."</th>\n";
	echo "    <th>".$fieldNames[receptionist]."</th>\n";
	echo "    <th>".$fieldNames[patientAcctMgr]."</th>\n";
	echo "    <th>".$fieldNames[officeMgr]."</th>\n";
	echo "    <th>".$fieldNames[provider]."</th>\n";
	echo "    <th>".$fieldNames[deaNumber]."</th>\n";
	echo "    <th>".$fieldNames[reactivate]."</th>\n";
	echo "  </tr>\n";
	
     $query = "SELECT * FROM `".$moduleDB."`.`user` WHERE `isActive` = 0 ORDER BY nameLast, nameFirst DESC";
     $result = $mysqli->query($query);
     while ($row = $result->fetch_object()) {
      echo "  <tr>\n";
		
	  //Userid
	  echo "    <td>\n";
	  echo $row->userId;
	  echo "    </td>\n";	
      //Input Boxes 
      $textInput = array('nameLast' , 'nameFirst'  ,  'emailAddress' , 'dob');
      foreach ($textInput as $textInput) {
        echo "    <td>".$row->$textInput."</td>\n";
      }
      //Check Boxes 
	  $checkInput = array('receptionist' , 'patientAcctMgr' , 'officeMgr' ,  'provider');
      foreach ($checkInput as $checkInput) {
	    if ($row->$checkInput == 1) {
          echo "      <td>X</td>\n";
        } else {
          echo "      <td>&nbsp;</td>\n";
        }		
	  }	 
		 
      //Provider Input Boxes 
	  if ($row->provider == 1) {	 
        $textInput = array('deaNumber');
        foreach ($textInput as $textInput) {
          echo "    <td>".$row->$textInput."</td>\n";
        }
	  } else {
         echo "    <td style=\"visibility:hidden;\">&nbsp;</td>\n";
	  }	  
      //Buttons
      $submitInput = array('reactivate' );
      foreach ($submitInput as $submitInput) {
        echo "    <td>\n      <input type=\"submit\" name=\"".$row->userId."_".$submitInput."\" id=\"".$row->userId."_".$submitInput."\"  value=\"".$fieldNames[$submitInput]."\"  
		onClick=\"authListButton('".$row->userId."', '".$submitInput."');\"/>\n           </td>\n";
      }			 
		 
      echo "  </tr>\n";		 
		
	} // end while ($row = $result->fetch_object())	
    $result->close;
    echo "</table>\n";
	
	
	echo "<h3>".$fieldNames[addUsers]."</h3>\n";
	echo "<form method=\"post\" action=\"authorizationList.php\">\n";
	echo "<p>\n";
	echo "<label for=\"addNameLast\">".$fieldNames[nameLast]."</label><input type=\"text\" id=\"addNameLast\" name=\"addNameLast\" />\n";
	echo "<label for=\"addNameFirst\">".$fieldNames[nameFirst]."</label><input type=\"text\" id=\"addNameFirst\" name=\"addNameFirst\" />\n";
	echo "<label for=\"addDOB\">".$fieldNames[DOB]."</label><input type=\"text\" id=\"addDOB\" name=\"addDOB\" />\n";
	echo "<label for=\"addEmail\">".$fieldNames[email]."</label><input type=\"text\" id=\"addEmail\" name=\"addEmail\" />\n";
	echo "<input type=\"submit\" id=\"addSubmit\" name=\"addSubmit\" value=\"".$fieldNames[addUsers]."\"/>\n";
	
	
	echo "</p>\n";	
	echo "</form>\n";
	
	
} //end Authorized Access
include "footer.php";
?>
