<?php session_start(); 
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
include "include/includeTranslation.php";
 
//Clean Text inputs
  $cleanRowId = htmlentities($_POST['user'], ENT_COMPAT, 'UTF-8');
  $cleanField = htmlentities($_POST['field'], ENT_COMPAT, 'UTF-8');
  $cleanValue = htmlentities($_POST['value'], ENT_COMPAT, 'UTF-8');
  $mysqlRowId = $mysqli->real_escape_string($cleanRowId);
  $mysqlField = $mysqli->real_escape_string($cleanField);
  $mysqlValue = $mysqli->real_escape_string($cleanValue);
  $textInput = array('nameLast' , 'nameFirst'  ,  'email' , 'dob' , 'deaNumber');
  $checkInput = array('receptionist' , 'patientAcctMgr' , 'officeMgr' ,  'provider');
  if (in_array($mysqlField, $textInput)) {
    $query = "UPDATE `".$moduleDB."`.`user` SET `".$mysqlField."` =  '".$mysqlValue."' WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
    $result = $mysqli->query($query);	
	if ($mysqli->affected_rows >=1) {
		echo $mysqlField." ".$fieldNames[textInputUpdate1]." ".$mysqlValue." ".$fieldNames[textInputUpdate2]." ".$mysqlRowId;
	} else {
		echo $fieldNames[updateFailed];
	}
  }
  if (in_array($mysqlField, $checkInput)) {
 	if ($mysqlValue == 'true') {
      $query = "UPDATE `".$moduleDB."`.`user` SET `".$mysqlField."` =  1 WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
      $result = $mysqli->query($query);
	  if ($mysqli->affected_rows >=1) {
		echo $fieldNames[$mysqlField]." ".$fieldNames[checkboxTrue];
		  
		  
		if ($mysqlField == 'provider') { //Add provider calendar table if it does not exist
			echo "providerTest";
	      $query = "CREATE TABLE `calendar".$mysqlRowId."` (
          `date` date NOT NULL,
          `05:30` varchar(128) NOT NULL DEFAULT '0',
          `06:00` varchar(128) NOT NULL DEFAULT '0',
          `06:30` varchar(128) NOT NULL DEFAULT '0',
          `07:00` varchar(128) NOT NULL DEFAULT '0',
          `07:30` varchar(128) NOT NULL DEFAULT '0',
          `08:00` varchar(128) NOT NULL DEFAULT '0',
          `08:30` varchar(128) NOT NULL DEFAULT '0',
          `09:00` varchar(128) NOT NULL DEFAULT '0',
          `09:30` varchar(128) NOT NULL DEFAULT '0',
          `10:00` varchar(128) NOT NULL DEFAULT '0',
          `10:30` varchar(128) NOT NULL DEFAULT '0',
          `11:00` varchar(128) NOT NULL DEFAULT '0',
          `11:30` varchar(128) DEFAULT '0',
          `12:00` varchar(128) NOT NULL DEFAULT '0',
          `12:30` varchar(128) NOT NULL DEFAULT '0',
          `13:00` varchar(128) NOT NULL DEFAULT '0',
          `13:30` varchar(128) NOT NULL DEFAULT '0',
          `14:00` varchar(128) NOT NULL DEFAULT '0',
          `14:30` varchar(128) DEFAULT '0',
          `15:00` varchar(128) NOT NULL DEFAULT '0',
          `15:30` varchar(128) NOT NULL DEFAULT '0',
          `16:00` varchar(128) NOT NULL DEFAULT '0',
          `16:30` varchar(128) NOT NULL DEFAULT '0',
          `17:00` varchar(128) DEFAULT '0',
          `17:30` varchar(128) NOT NULL DEFAULT '0',
          `18:00` varchar(128) NOT NULL DEFAULT '0',
          `18:30` varchar(128) NOT NULL DEFAULT '0',
          `19:00` varchar(128) NOT NULL DEFAULT '0',
          `19:30` varchar(128) NOT NULL DEFAULT '0',
          `20:00` varchar(128) NOT NULL DEFAULT '0',
          `20:30` varchar(128) NOT NULL DEFAULT '0',
          `21:00` varchar(128) NOT NULL DEFAULT '0'
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        --
        -- Indexes for table `calendar".$mysqlRowId."`
        --
        ALTER TABLE `calendar".$mysqlRowId."`
          ADD PRIMARY KEY (`date`);
        ";
	    $query2 = "CREATE TABLE `calendar".$mysqlRowId."` like `calendarClinic`";
			echo $query2;
        $mysqli->query($query2);
		}  
		  
	  } else {
		echo $fieldNames[updateFailed];
	  }
	}
	if ($mysqlValue == 'false') {
      $query = "UPDATE `".$moduleDB."`.`user` SET `".$mysqlField."` =  0 WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
      $result = $mysqli->query($query);
	  if ($mysqli->affected_rows >=1) {
		echo $fieldNames[$mysqlField]." ".$fieldNames[checkboxFalse];
	  } else {
		echo $fieldNames[updateFailed];
	  }
	}	 
  }
  if ($mysqlField == 'deactivate') {
      $query = "UPDATE `".$moduleDB."`.`user` SET `isActive` =  0, `dateAcctDeleted` = CURDATE() WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
      $result = $mysqli->query($query);
	  if ($mysqli->affected_rows >=1) {
		echo $fieldNames[$mysqlRowId]." ".$fieldNames[deactivated];
	  } else {
		echo $fieldNames[updateFailed];
	  }
  } 
  if ($mysqlField == 'reactivate') {
      $query = "UPDATE `".$moduleDB."`.`user` SET `isActive` =  1, `dateAcctDeleted` = null WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
	  $result = $mysqli->query($query);
	  if ($mysqli->affected_rows >=1) {
		echo $fieldNames[$mysqlRowId]." ".$fieldNames[reactivated];
	  } else {
		echo $fieldNames[updateFailed];
	  }
  } 
  if ($mysqlField == 'resetPassword') {
	  //Reset Password and send to user
	  $randomPassword = bin2hex(openssl_random_pseudo_bytes(6));
      $hashNewPass = password_hash($randomPassword,PASSWORD_DEFAULT);
      //Update database with new hashed password and date for the pass change
      $query = "UPDATE `".$moduleDB."`.`user` SET `password` = '".$hashNewPass."', `datePwdLastUpdate` = CURDATE() WHERE `userId` = '".$mysqlRowId."'  ";
      $result = $mysqli->query($query);
	  if ($mysqli->affected_rows >=1) {
		echo $fieldNames[passwordReset]." ".$mysqlRowId;
	  } else {
		echo $fieldNames[updateFailed];
	  }
	  
      //Email user
      $query = "SELECT `email`, `nameLast`, `nameFirst` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$mysqlRowId."' LIMIT 1";
      $result = $mysqli->query($query);
      while ($row = $result->fetch_object()) {
         $body = $fieldNames[passwordResetEmailGreeting]." ".$row->nameFirst." ".$row->nameLast.",\n ".$fieldNames[passwordResetEmailBody1]." ".$randomPassword."\n ".$fieldNames[passwordResetEmailBody2];
		 mail($row->email, $fieldNames[passwordResetEmailSubject], $body);
	  }
      
  }
