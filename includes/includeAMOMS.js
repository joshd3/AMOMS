var xmlHttp = false;
   function makePOSTRequest(url, parameters) {


      xmlHttp = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         xmlHttp = new XMLHttpRequest();
         if (xmlHttp.overrideMimeType) {
         	// set type accordingly to anticipated content type
            //xmlHttp.overrideMimeType('text/xml');
            xmlHttp.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!xmlHttp) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      xmlHttp.onreadystatechange=stateChanged;
      xmlHttp.open('POST', url, true);
      xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xmlHttp.setRequestHeader("Content-length", parameters.length);
      xmlHttp.setRequestHeader("Connection", "close");
      xmlHttp.send(parameters);
   }

function stateChanged() { 
  if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") {
   if(operation == 'updateField') {
     document.getElementById('feedback').innerHTML=xmlHttp.responseText;
   }
   if(operation == 'updateTreatment') {
     document.getElementById('treatment').innerHTML=xmlHttp.responseText;
   }
   if(operation == 'toggleAvailable') {
     statusSpan = myDivId;
     document.getElementById( statusSpan ).className='calendarDefault';
     document.getElementById('feedback').innerHTML=xmlHttp.responseText;

   }
   if(operation == 'toggleUnavailable') {
     statusSpan = myDivId;
     document.getElementById( statusSpan ).className='calendarNotAvailable';
     document.getElementById('feedback').innerHTML=xmlHttp.responseText;

   }


}
}

function updateContactField(patientElement , idElement) {
  var poststr = "field=" + encodeURI( idElement ) + 
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( idElement ).value );
  makePOSTRequest('contactUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updateHistory(patientElement , idElement) {
  var poststr = "field=" + encodeURI( idElement ) + 
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( idElement ).value );
  makePOSTRequest('historyUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updateHistoryBox(patientElement , idElement, keyElement) {
  var poststr = "field=" + encodeURI( idElement ) + 
    "&key=" + encodeURI( keyElement ) +
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( idElement + keyElement ).checked );
  makePOSTRequest('historyUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updatePrescription(patientElement , fieldElement, idElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) + 
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).value );
  makePOSTRequest('prescriptionUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updatePrescriptionCheck(patientElement , fieldElement, idElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) + 
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).checked );
  makePOSTRequest('prescriptionUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updatePrescriptionEnd(patientElement , idElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&patient=" + encodeURI( patientElement ) + 
    "&value=end";
  makePOSTRequest('prescriptionUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updateAppointment(patientElement , fieldElement, rowElement) {
  var poststr = "rowId=" + encodeURI( rowElement ) + 
    "&field=" + encodeURI( fieldElement ) + 
    "&patient=" + encodeURI( patientElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + rowElement ).value );
  makePOSTRequest('appointmentUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function updateAppointmentSelect(patientElement, fieldElement, rowElement, updateElement) {
  var e = document.getElementById( fieldElement + rowElement );
  var strUser = e.options[e.selectedIndex].title;
  document.getElementById( updateElement ).innerHTML= strUser;
  var poststr = "rowId=" + encodeURI( rowElement ) + 
    "&field=" + encodeURI( fieldElement ) + 
    "&patient=" + encodeURI( patientElement ) + 
    "&value=" + encodeURI( document.getElementById( fieldElement + rowElement ).value );
  makePOSTRequest('appointmentUpdate.php', poststr);
    window.operation = 'updateField'; 
}

//Calendar Functions
function calendarBaseUpdate(fieldElement , rowElement) {
  var poststr = "operation=base" +
    "&rowId=" + encodeURI( rowElement ) + 
    "&field=" + encodeURI( fieldElement ) + 
    "&value=" + encodeURI( document.getElementById( fieldElement  ).value );
  makePOSTRequest('calendarUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function calendarAvailabilityUpdate( dateElement , hourElement, fieldElement, toggleElement) {
  var poststr = "operation=providerCalendar" +
    "&rowId=" + encodeURI( dateElement ) + 
    "&field=" + encodeURI( hourElement ) + 
    "&value=" + encodeURI( document.getElementById( fieldElement  ).innerText ) +
    "&toggle=" + encodeURI( toggleElement );
  makePOSTRequest('calendarUpdate.php', poststr);
    if ( toggleElement == "calendarDefault" ||   toggleElement == "calendarApptScheduled"    ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleUnavailable';
    } 
    if (  toggleElement == "calendarNotAvailable" ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleAvailable';
    } 
}

function calendarMasterUpdate( dateElement , hourElement, fieldElement, toggleElement) {
  var poststr = "operation=masterCalendar" +
    "&rowId=" + encodeURI( dateElement ) + 
    "&field=" + encodeURI( hourElement ) + 
    "&value=" + encodeURI( document.getElementById( fieldElement  ).innerText ) +
    "&toggle=" + encodeURI( toggleElement );
  makePOSTRequest('calendarUpdate.php', poststr);
    if ( toggleElement == "calendarDefault" ||   toggleElement == "calendarApptScheduled"    ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleUnavailable';
    } 
    if (  toggleElement == "calendarNotAvailable" ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleAvailable';
    } 
}

function calendarSelectTreatment( fieldElement) {
  var poststr = "operation=selectTreatment" + 
    "&value=" + encodeURI( document.getElementById( fieldElement  ).value );
  makePOSTRequest('calendarUpdate.php', poststr);
    window.operation = 'updateTreatment'; 
}

function calendarSchedulePatient( dateElement , hourElement, fieldElement, toggleElement, patientElement, treatmentElement , providerElement) {
  var poststr = "operation=schedulePatient" +
    "&provider=" + encodeURI( providerElement ) + 
    "&patient=" + encodeURI( patientElement ) + 
    "&treatment=" + encodeURI( document.getElementById( treatmentElement  ).innerText ) + 
    "&rowId=" + encodeURI( dateElement ) + 
    "&field=" + encodeURI( hourElement ) + 
    "&value=" + encodeURI( document.getElementById( fieldElement  ).innerText ) +
    "&toggle=" + encodeURI( toggleElement );
  makePOSTRequest('calendarUpdate.php', poststr);
    if ( toggleElement == "calendarDefault"  ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleUnavailable';
    } 
    if (   toggleElement == "calendarApptScheduled"   ) {
     window.myDivId = "td" + fieldElement;
     window.operation = 'toggleAvailable';
    } 
}

/////////////////////////////////////////////////////////////////////////////////////
//Authorization list functions
function authListCheckbox(userElement , fieldElement) {
  var poststr = "user=" + encodeURI( userElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( userElement + "_" + fieldElement ).checked );
  makePOSTRequest('authorizationListUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function authListUpdate(userElement , fieldElement) {
  var poststr = "user=" + encodeURI( userElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( userElement + "_" + fieldElement ).value );
  makePOSTRequest('authorizationListUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function authListButton(userElement , fieldElement) {
  var poststr = "user=" + encodeURI( userElement ) + 
    "&field=" + encodeURI( fieldElement );
  makePOSTRequest('authorizationListUpdate.php', poststr);
    window.operation = 'updateField'; 
}

/////////////////////////////////////////////////////////////////////////////////////
//Treatment list functions
function updateTreatment(idElement , fieldElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).value );
  makePOSTRequest('treatmentUpdate.php', poststr);
    window.operation = 'updateField'; 
}

/////////////////////////////////////////////////////////////////////////////////////
//Billing functions
function updateTransaction(idElement , fieldElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).value );
  makePOSTRequest('billingUpdate.php', poststr);
    window.operation = 'updateField'; 
}
function emailBill(idElement , fieldElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).value );
  makePOSTRequest('billingUpdate.php', poststr);
    window.operation = 'updateField'; 
}
function updateContactNote(idElement , fieldElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=" + encodeURI( fieldElement ) +
    "&value=" + encodeURI( document.getElementById( fieldElement + idElement ).value );
  makePOSTRequest('billingUpdate.php', poststr);
    window.operation = 'updateField'; 
}

function insuranceSubmit(idElement) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=insuranceSubmit";
  makePOSTRequest('billingUpdate.php', poststr);
    window.operation = 'updateField'; 
}

/////////////////////////////////////////////////////////////////////////////////////
//Reporting functions
function apptReminderPhone(idElement ) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&field=apptReminderPhone";
  makePOSTRequest('reportUpdate.php', poststr);
    window.operation = 'updateField'; 
}
function apptReminder( idElement , apptTime , apptDate , apptProvider ) {
  var poststr = "rowId=" + encodeURI( idElement ) + 
    "&apptTime=" + encodeURI( apptTime ) +
    "&apptDate=" + encodeURI( apptDate ) +
    "&apptProvider=" + encodeURI( apptProvider );
makePOSTRequest('reportUpdate.php', poststr);
    window.operation = 'updateField'; 
}
