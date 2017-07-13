<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <link href="include/includeAMOMS.css" rel="stylesheet" type="text/css" />
   <link rel="stylesheet" media="print" href="include/print.css" type="text/css" />
   <script type="text/javascript" src="include/includeAMOMS.js"></script>

</head>


<body>


<?php
  echo "<div id=\"feedback\" class=\"feedback\"><br /></div><br />\n";
$languagePref = $_SESSION['languagePref'];
//Database Connection
$moduleDB = 'lwcsurvey_amoms';
include "include/includeSQLiHost.php";
//Patient ID of current patient
if (strlen($_GET['pid']) > 0) {
  $patientId = htmlentities($_GET['pid'], ENT_QUOTES);
  $mySQLiPatientId = $mysqli->real_escape_string($patientId);
  $query = "SELECT `namePreferred` FROM `".$moduleDB."`.`patient` WHERE `patientId` = '".$mySQLiPatientId."'"; 
  $result = $mysqli->query($query);
  if ($result->num_rows == 1) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $patientName = $row[namePreferred];
    $patientId =  $mySQLiPatientId;
  } else {
    $patientId = 0;
  }
  $result->close();
} else {
  $patientId = 0;
}
//Identify User Roles based on Login ID
  $accessCheck = 0;
  $query = "SELECT `receptionist` , `provider` , `patientAcctMgr`, `officeMgr` FROM `".$moduleDB."`.`user` WHERE `isActive` = 1 AND `dateAcctDeleted` IS NULL AND `userId` like '".$_SESSION['amomsId']."'"; 
  $result = $mysqli->query($query);
  $row = $result->fetch_array(MYSQLI_ASSOC);
  //Assign roles
  if ($row[receptionist] == 1) { $accessReceptionist = 1; $accessCheck++; } // If receptionist role assigned, enable receptionist access
  if ($row[provider] == 1) { $accessProvider = 1; $accessCheck++; } // If provider role assigned, enable provider access
  if ($row[patientAcctMgr] == 1) { $accessPatientAcctMgr = 1; $accessCheck++; } // If Patient Account Manager role assigned, enable Patient Account Manager  access
  if ($row[officeMgr] == 1) { $accessOfficeMgr = 1; $accessCheck++; } // If Office Manager role assigned, enable Office Manager access
  // close result set
  $result->close();
echo "  <div id=\"titleBar\">\n";
echo "    <span id=\"title\">AMOMS</span>\n";
if (strlen($patientId) > 1) {
  echo "    <span id=\"patientId\" class=\"patientId\">".$patientId."</span >\n"; 
  echo "    <span id=\"patientName\" class=\"patientName\">".$patientName."</span >\n"; 
} else {
  echo "    <span class=\"patientId hidden\" > </span >\n"; 
  echo "    <span class=\"patientName hidden\"> </span >\n"; 
}
if ($accessOfficeMgr == 1) {
  echo "    <span id=\"treatment\" class=\"button treatment\"><a class=\"buttonlink\" href=\"treatment.php\">Tx</a></span>\n"; 
  echo "    <span id=\"userAdmin\" class=\"button userAdmin\"><a class=\"buttonlink\" href=\"authorizationList.php\">";
  if ($languagePref == 'spa') {echo "Administrar usuarios"; } else {echo "Administer Users"; } 
		  echo "</a>";
  echo "</span >\n";
} else {
  echo "    <span class=\"treatment hidden\"> </span >\n"; 
  echo "    <span class=\"userAdmin hidden\"> </span >\n"; 
}
 
if ($accessCheck > 0) {
  echo "    <span class=\"button \" id=\"myProfile\"><a class=\"buttonlink\" href=\"myProfile.php\">";
  if ($languagePref == 'spa') {echo "Mi Perfil"; } else {echo "My Profile"; } 
	  echo "</a>";
} else {
  echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessCheck > 0) {
  echo "    <span class=\"button\" id=\"logout\"><a class=\"buttonlink\" href=\"logout.php\">";
  if ($languagePref == 'spa') {echo "Cerrar Sesión"; } else {echo "Logout"; } 
  echo "</a>";
} else {
  echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
echo "  </div> <!-- Close Title Bar Div -->\n";
echo "  <div id=\"NavigationBar\">\n";
if ($accessCheck > 0) {
  echo "    <span class=\"button\" id=\"search\"><a href=\"search.php\" class=\"buttonlink\">";
  if ($languagePref == 'spa') {echo "Buscar"; } else {echo "Search"; } 
  echo "</a>";
} else {
 echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessReceptionist == 1 || $accessOfficeMgr == 1) {
  echo "    <span id=\"contactInfo\" class=\"button\"><a class=\"buttonlink\" href=\"contact.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Información del contacto"; } else {echo "Contact Information"; } 
  echo "</a>";
} else {
 echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessProvider == 1) {
  echo "    <span id=\"medicalHistory\" class=\"button\"><a class=\"buttonlink\" href=\"history.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Historial Médico"; } else {echo "Medical History"; } 
  echo "</a>";
  echo "</span >\n";
  echo "    <span id=\"appointment\" class=\"button\"> <a class=\"buttonlink\" href=\"appointment.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Cita Medica"; } else {echo "Appointment"; } 
	  echo "</a>";
  echo "</span >\n";
  echo "    <span id=\"prescription\" class=\"button \"><a class=\"buttonlink\" href=\"prescription.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Prescripción"; } else {echo "Prescription"; } 
  echo "</a>";
} else {
  echo "    <span class=\"button hidden\"> </span>\n";
  echo "    <span class=\"button hidden\"> </span>\n";
  echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessPatientAcctMgr == 1 || $accessOfficeMgr == 1) {
  echo "    <span id=\"billing\" class=\"button \"><a class=\"buttonlink\" href=\"billing.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Facturación"; } else {echo "Billing"; } 
	  echo "</a>";
	
} else {
  echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessCheck > 0) {
  echo "    <span class=\"button \" id=\"reports\"><a class=\"buttonlink\" href=\"report.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Informes"; } else {echo "Reports"; } 
	  echo "</a>";
} else {
  echo "    <span class=\"button hidden\">";
}
echo "</span >\n";
if ($accessReceptionist == 1|| $accessProvider == 1 || $accessOfficeMgr == 1) {
  echo "    <span class=\"button \"> <a class=\"buttonlink\" href=\"calendar.php?pid=".$patientId."\">";
  if ($languagePref == 'spa') {echo "Calendario"; } else {echo "Calendar"; } 
	  echo "</a>";
} else {
  echo "    <span class=\"button hidden\">";
}
echo  "</span>\n";
?>

</div> <!-- NavigationBar -->
