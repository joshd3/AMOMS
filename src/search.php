<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>AMOMS - Search</title>
<script type="text/javascript">
  window.onload = function() {
    document.getElementById('search').className = 'active';
  };
</script>

<?php
 include "header.php";
//Redirect to login screen if no session user name
if (strlen($_SESSION['amomsId']) == 0) {
  include "login.php";
  include "footer.php";
} else {
?>
  <div id="mainContent">
    <form method="post" action="search.php">
      <p>
<?php
        if ($languagePref == 'spa') {
          echo "        Búsqueda por identificación del paciente, primeros caracteres o apellido completo, y / o primeros caracteres o nombre completo.<br />\n"; 
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"patientId\" name=\"patientId\" value=\"Identificación del paciente\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"nameLast\" name=\"nameLast\" value=\"Apellido\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"nameFirst\" name=\"nameFirst\" value=\"Nombre de Pila\" />\n";
          echo "        <input class=\"inputSubmit\"   type=\"submit\" id=\"patientSearchSubmit\" name=\"patientSearchSubmit\" value=\"Buscar\" />\n";
        } else { 
          echo "        Search by patient id, first characters of or full last name, and/or  first characters of or full first name. <br />\n"; 
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"patientId\" name=\"patientId\" value=\"Patient Identification Number\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"nameLast\" name=\"nameLast\" value=\"Last Name\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"nameFirst\" name=\"nameFirst\" value=\"First Name\" />\n";
          echo "        <input class=\"inputSubmit\"   type=\"submit\" id=\"patientSearchSubmit\" name=\"patientSearchSubmit\" value=\"Search\" />\n";
        }
?>
      </p>
    </form>

<?php
   if ($_POST['patientSearchSubmit']) {
      $sqlWHERE = '';
     if ($_POST['patientId'] != 'Patient Identification Number' &&  $_POST['patientId'] != 'Identificación del paciente') {  $sqlWHERE .= "`patientId` LIKE '".$mysqli->real_escape_string($_POST['patientId'])."%' AND ";}
     if ($_POST['nameLast'] != 'Last Name' && $_POST['nameLast'] != 'Apellido') {  $sqlWHERE .= "`nameLast` LIKE '".$mysqli->real_escape_string($_POST['nameLast'])."%' AND "; }
     if ($_POST['nameFirst'] != 'First Name' && $_POST['nameFirst'] != 'Nombre de Pila') {  $sqlWHERE .= "`nameFirst` LIKE '".$mysqli->real_escape_string($_POST['nameFirst'])."%' AND "; }
    $query = "SELECT `patientId`, `nameLast`, `nameFirst`, `namePreferred`, `dob` FROM `".$moduleDB."`.`patient` WHERE ".substr($sqlWHERE,0,-5);
    $result = $mysqli->query($query);
    
    if ($result->num_rows == 0)  {
      if ($languagePref == 'spa') {  echo "    <h3>No se han encontrado resultados. Inténtalo de nuevo.</h3>\n"; } else {  echo "    <h3>No results found. Please Try Again</h3>\n"; }
    } else {
      if ($languagePref == 'spa') {
        echo "    <table>\n      <tr>\n        <th>Identificación del paciente</th>\n        <th>Apellido</th>\n        <th>Nombre de Pila</th>\n        <th>Nombre preferido</th>\n        <th>Fecha de nacimiento</th>\n      </tr>\n";
      } else {
        echo "    <table>\n      <tr>\n        <th>Patient Id</th>\n        <th>Last Name</th>\n        <th>First Name</th>\n        <th>Preferred Name</th>\n        <th>Date of Birth</th>\n      </tr>\n";
      }
    
     while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "      <tr>\n        <td><a href=\"search.php?pid=".$row['patientId']."\">".$row['patientId']."</a></td>\n        <td>".$row['nameLast']."</td>\n        <td>".$row['nameFirst']."</td>\n        <td>".$row['namePreferred']."</td>\n        <td>".$row['dob']."</td>\n      </tr>";
     }
    echo "    </table>\n";
    $result->close();
  } // end     if ($result->num_rows == 0)  {
} // end    if ($_POST['patientSearchSubmit']) {
if ($accessReceptionist == 1 || $accessOfficeMgr ==1) {
  if ($languagePref == 'spa') { echo "  <h3>Añadir nuevo paciente</h3>\n"; } else { echo "  <h3>Add New Patient</h3>\n"; }
  echo "  <form id=\"newPatient\" method=\"post\" action=\"search.php\">\n";
  echo "    <p>\n";
        if ($languagePref == 'spa') {
          echo "        Por favor rellene ambas casillas<br />\n"; 
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"addNameLast\" name=\"addNameLast\" value=\"Apellido\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"addNameFirst\" name=\"addNameFirst\" value=\"Nombre de Pila\" />\n";
          echo "        <input class=\"inputSubmit\"   type=\"submit\" id=\"newPatient\" name=\"newPatient\" value=\"Anadir\" />\n";
        } else { 
          echo "        Please Fill In Both Boxes<br />\n"; 
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"addNameLast\" name=\"addNameLast\" value=\"Last Name\" />\n";
          echo "        <input class=\"inputBox\" onClick=\"this.select();\"  type=\"text\" id=\"addNameFirst\" name=\"addNameFirst\" value=\"First Name\" />\n";
          echo "        <input class=\"inputSubmit\"   type=\"submit\" id=\"newPatient\" name=\"newPatient\" value=\"Add\" />\n";
        }
  echo "    </p>\n";
  echo "  </form>\n";
  if ($_POST['newPatient']) {
         $firstInit = substr($mysqli->real_escape_string($_POST['addNameFirst']),0,1);
         $lastName = $mysqli->real_escape_string($_POST['addNameLast']);
         $query = "SELECT `patientId` FROM `".$moduleDB."`.`patient` WHERE `nameFirst` LiKE '".$firstInit."%' AND `nameLast` = '".$lastName."'";
         $result = $mysqli->query($query);
         $nextSequence = $result->num_rows;
         $result->close();
          $query = "INSERT INTO `patient` (`patientID`, `nameFirst`, `nameLast`) VALUES ('".strtolower($firstInit).strtolower($lastName).$nextSequence."', '".$mysqli->real_escape_string($_POST['addNameFirst'])."', '".$lastName."')";
          $result = $mysqli->query($query);
          if ($mysqli->affected_rows == 1) {
              if ($languagePref == 'spa') { echo "  <h4>Paciente añadido con éxito. Haga clic en el enlace de abajo para continuar.</h4>\n"; } else { echo "  <h4>Patient Successfully Added. Click the link below to continue.</h4>\n"; }
             echo "    <p><a href=\"search.php?pid=".strtolower($firstInit).strtolower($lastName).$nextSequence."\">".strtolower($firstInit).strtolower($lastName).$nextSequence."</a></p>\n";
          }
  }
  
}
?>
  </div>



<?php
  include "footer.php";
}
?>
