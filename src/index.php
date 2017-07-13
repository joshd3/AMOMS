<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS</title>

<?php 
if ($_POST['english'] || $_POST['spanish']) {
   $moduleDB = 'lwcsurvey_amoms';
   $mysqli = new mysqli('localhost', 'amomsdb', 'Amer!canGene515', $moduleDB);
   $cleanUserName  = htmlentities($_POST['userId'], ENT_QUOTES);
   $mySQLiUserName = $mysqli->real_escape_string($cleanUserName);
   $query = "SELECT `password` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$mySQLiUserName."'";
   $result = $mysqli->query($query);
   $row = $result->fetch_array(MYSQLI_ASSOC);
if (password_verify($_POST['pwd'], $row['password'])) {
   $_SESSION['amomsId'] =  $mySQLiUserName;
} else {
    echo 'Invalid password.';
   //Need counter and means of temp disabling account
}
   $result->close();
   $mysqli->close(); 
      if ($_POST['english']) { $_SESSION['languagePref'] = 'eng'; }
      if ($_POST['spanish']) { $_SESSION['languagePref'] = 'spa'; }
} 
  
   
  include "header.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  ///////////////////////////////////////////////////////////////
  //Provide Credentials to Retrieve Username	 
	if ($_POST[forgotUserName]) {
    echo "<div id=\"mainContent\">\n";
    echo "  <form id=\"forgotUserName\" method=\"post\" action=\"index.php\">\n";
    echo "  <p>Date of Birth / Fecha de nacimiento \"YYYY-MM-DD\"\n";
    echo "  <br /><input type=\"text\" name=\"dob\" id=\"dob\" />\n";
    echo "  <br />Email / Correo Electrónico\n";
    echo "  <br /><input type=\"text\" name=\"email\" id=\"email\" />\n";
    echo "  <br /><br /><input name=\"forgotUserNameSubmit\" type=\"submit\" value=\"Submit/Enviar\" />\n";
    echo "  </p>\n";	  
    echo "</div>\n";
    include "footer.php";
  } elseif ($_POST[forgotUserNameSubmit]) {
  ///////////////////////////////////////////////////////////////
  //Retreive Username with account email and dob	  
   echo "<div id=\"mainContent\">\n";
   $mysqli = new mysqli('localhost', 'amomsdb', 'Amer!canGene515', $moduleDB);
   $cleanEmail  = htmlentities($_POST['email'], ENT_QUOTES);
   $mySQLiEmail = $mysqli->real_escape_string($cleanEmail);
   $cleanDOB  = htmlentities($_POST['dob'], ENT_QUOTES);
   $mySQLiDOB = $mysqli->real_escape_string($cleanDOB);
  
   $query = "SELECT `userId` FROM `".$moduleDB."`.`user` WHERE `email` = '".$mySQLiEmail."' && `dob` = '".$mySQLiDOB."'";
   $result = $mysqli->query($query);
   if ($result->num_rows == 1) {
	 $row = $result->fetch_object();
	 mail($mySQLiEmail, 'AMOMS UserName - Nombre de usuario de AMOMS', $row->userId);
	 echo "<p>Your User Name has been sent to the email address on file.</p>";
	 echo "<p>Su nombre de usuario se ha enviado a la dirección de correo electrónico en el archivo.</p>";
   } else {
	 echo "<p>Your account credentials could not be found, please contact your office manager for assistance</p>";
	 echo "<p>No se pudo encontrar las credenciales de su cuenta, póngase en contacto con su gerente de oficina para obtener asistencia.</p>";
   }
    echo "</div>\n";
    include "footer.php";	   
  } elseif ($_POST[forgotPassword]) {
  ///////////////////////////////////////////////////////////////
  //Provide Credentials to Reset Password		  
    echo "<div id=\"mainContent\">\n";
    echo "<form id=\"forgotPassword\" method=\"post\" action=\"index.php\">\n";
    echo "  <p>UserName / Nombre de Usario\n";
    echo "  <br /><input type=\"text\" name=\"userId\" id=\"userId\" />\n";
    echo "  <br />Email / Correo Electrónico\n";
    echo "  <br /><input type=\"text\" name=\"email\" id=\"email\" />\n";
    echo "  <br /><br /><input name=\"forgotPasswordSubmit\" type=\"submit\" value=\"Submit/Enviar\" />\n";
    echo "  </p>\n";
    echo "</div>\n";
    include "footer.php";
  } elseif ($_POST[forgotPasswordSubmit]) {
  ///////////////////////////////////////////////////////////////
  //Reset Password with username and account email	  
   echo "<div id=\"mainContent\">\n";
   $mysqli = new mysqli('localhost', 'amomsdb', 'Amer!canGene515', $moduleDB);
   $cleanEmail  = htmlentities($_POST['email'], ENT_QUOTES);
   $mySQLiEmail = $mysqli->real_escape_string($cleanEmail);
   $cleanUserName  = htmlentities($_POST['userId'], ENT_QUOTES);
   $mySQLiUserName = $mysqli->real_escape_string($cleanUserName);
  
   $query = "SELECT `userId` FROM `".$moduleDB."`.`user` WHERE `email` = '".$mySQLiEmail."' && `userId` = '".$mySQLiUserName."'";
   $result = $mysqli->query($query);
   $row = $result->fetch_object();
   if ($result->num_rows == 1) {
     $randomPassword = bin2hex(openssl_random_pseudo_bytes(6));
     $hashNewPass = password_hash($randomPassword,PASSWORD_DEFAULT);
     $query = "UPDATE `".$moduleDB."`.`user` SET `password` = '".$hashNewPass."', `datePwdLastUpdate` = CURDATE() WHERE `userId` = '".$row->userId."'  ";
     $result = $mysqli->query($query);
	 $body = "Your password has been reset to:\r\nSu contraseña se ha restablecido a:\r\n".$randomPassword;
	 mail($mySQLiEmail, 'AMOMS Password/Contraseña', $body);
	 echo "<p>Your password has been reset and sent to the email address on file.</p>";
	 echo "<p>Su contraseña ha sido restablecida y enviada a la dirección de correo electrónico en el archivo.</p>";
   } else {
	 echo "<p>Your account credentials could not be found, please contact your office manager for assistance</p>";
	 echo "<p>No se pudo encontrar las credenciales de su cuenta, póngase en contacto con su gerente de oficina para obtener asistencia.</p>";
   }
	$result->close;
    echo "</div>\n";
    include "footer.php";		  
	  
  } else {
    include "login.php";
    include "footer.php";
  }
} else {
  include "footer.php";
}
?>
