<?php  
    session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>AMOMS -  My Profile/Mi Perfil</title>
    
 <script type="text/javascript">
  window.onload = function() {
    document.getElementById('myProfile').className = 'active';
  };
</script>   
    
<?php
include "header.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
}
//Begin authorized access
else {    
echo "  <div id=\"mainContent\">\n";
//Display form based on language preference
if ($languagePref == 'spa'){
echo "<form id=\"passChangeForm\" action=\"myProfile.php\" method=\"POST\">";
echo " Contraseña actual <br>";
echo "<input type=\"password\" name=\"currentPassword\" id=\"currentPassword\" /> <br> ";
echo "Nueva Contraseña actual <br>";
echo "<input type=\"password\" name=\"newPassword\" id=\"newPassword\" /> <br> ";
echo " Repita La Nueva Contraseña <br>";
echo "<input type=\"password\" name=\"repeatNewPassword\" id=\"repeatNewPassword\" /> <br> ";
echo "<input type=\"submit\" name=\"submitNewPassword\" id=\"submitNewPassword\" value=\"Enviar cambio de contraseña\" />";
echo "</form>\n";
}
 
else
{
echo "<form id=\"passChangeForm\" action=\"myProfile.php\" method=\"POST\">";
echo " Current Password <br>";
echo "<input type=\"password\" name=\"currentPassword\" id=\"currentPassword\" /> <br> ";
echo " New Password <br>";
echo "<input type=\"password\" name=\"newPassword\" id=\"newPassword\" /> <br> ";
echo " Repeat New Password <br>";
echo "<input type=\"password\" name=\"repeatNewPassword\" id=\"repeatNewPassword\" /> <br> ";
echo "<input type=\"submit\" name=\"submitNewPassword\" id=\"submitNewPassword\" value=\"Submit Password Change\" />";
echo "</form>\n";
} 
//After clicking submit to change password...
if ($_POST['submitNewPassword'])
{
    $username = $_SESSION['amomsId'];
    
 //Grab inputs and trim spaces before and after the string to properly check lengeh
    $currentPass = trim($_POST['currentPassword']);
    $newPass = trim($_POST['newPassword']);
    $repeatNewPass = trim($_POST['repeatNewPassword']);
  
    //Sanitize inputs
    
  $sqlCurrentPass = $mysqli->real_escape_string($currentPass);
  $sqlNewPass = $mysqli->real_escape_string($newPass);
  $sqlRepeatNewPass = $mysqli->real_escape_string($repeatNewPass);
     
    
    $queryCurrentPass = "SELECT `password` FROM `".$moduleDB."`.`user` WHERE `userId` = '".$username."'";
    $result = $mysqli->query($queryCurrentPass);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $dbCurrentPass = $row['password'];
    
    //Check that all fields have been filled in
    if ($currentPass && $newPass && $repeatNewPass)
    
    {       
        if (password_verify($sqlCurrentPass, $dbCurrentPass))
        {
            if (strlen($sqlNewPass) >= 7)
            {
                if ($sqlNewPass == $sqlRepeatNewPass)
                {
                    //Hash the new password to store into database
                    $hashNewPass = password_hash($sqlRepeatNewPass,PASSWORD_DEFAULT);
                    
                    //Update database with new hashed password and date for the pass change
                    $mysqli->query("UPDATE `".$moduleDB."`.`user` SET `password` = '$hashNewPass', `datePwdLastUpdate` = NOW() WHERE `userId` = '$username'  ");
                    
                    if ($languagePref == 'spa')
                    {
                        echo "<h3>Se ha actualizado tu contraseña </h3> <br>";
                        echo '<META HTTP-EQUIV=REFRESH CONTENT="3; URL=http:index.php">.';
                    
                       // remove any session variables
                        session_unset();
                        // End session
                        session_destroy(); 
                    }
                   
                    else
                    {
                        echo "<h3>Your password has been updated</h3> <br>";
                        echo '<META HTTP-EQUIV=REFRESH CONTENT="3; URL=http:index.php">.';
                         
                        session_unset();
                        session_destroy(); 
                    }
                }
                else 
                 {
                    if ($languagePref == 'spa')
                    {
                        echo "<h3>El nuevo pase no coincide</h3>";
                    }
                    else
                    {
                        echo "<h3>New pass does not match</h3>";
                    }
                 }
            }
            else
            {
                if ($langugePref == 'spa')
                {
                    echo "<h3>La nueva contraseña debe tener al menos 7 caracteres</h3>";
                }
                
                else 
                {
                    echo "<h3>New password must be at least 7 characters</h3>";
                }
            }
        }
        else {
            if ($languagePref == 'spa')
            {
                echo "<h3>La contraseña actual no coincide</h3>";
            }
            else
            {
                echo "<h3>Current password does not match</h3>";
            }
        }
    }
  
     else
     {
         if ($languagePref == 'spa')
         {
            echo "<h3<Todos los campos deben rellenarse</h3>";
         }
         
         else
         {
             echo "<h3>All Fields must be filled in </h3> ";
         }
     }
        $mysqli->close();
}       
      
    
  
    
    
echo "  </div>\n"; //close Main Content Div
} //end authorized access
include "footer.php";
?>
