<?php
  if ($languagePref == 'spa') {
    $fieldNames = array( 
		'allergies' => 'Alergias' , 
		'newAppt' => 'Nueva cita médica' , 
		'apptDate' => 'Cita Fecha' , 
        'bloodPressure' => 'Presión Sanguínea',
		'pulse' => 'Frecuencia del Pulso',
		'respiration' => 'Ritmo Respiratorio',
		'height' => 'Cuánto mides(cm)',
		'weight' => 'Cuánto pesas (kg)',
		'reason' => 'Queja',
		'diagnosis' => 'Diagnostico',
		'notes' => 'Notas',
		'treatment' => 'Tratamiento' ,
		'followup' => 'Introduzca el número de semanas hasta la próxima visita.' ,
		'nextAppt' => 'Próxima cita' ,
		'followupApptSet' => 'Se requiere cita de seguimiento para la semana de',		
		'diagnosticCode' => 'Código de diagnóstico',
		'namePreferred' => 'Nombre Preferido',
		'callBackDate' => 'Fecha de devolución de llamada',
		'phone' => 'Número de teléfono',		
		
	     //Billing
		'acctBalance' => 'Saldo dela cuenta' ,
		'acctStatus' => 'Estado dela cuenta' ,
		'paymentAmount' => 'Monto del pago' ,
		'paymentType' => 'Método del pago',
		'payor' => 'Pagador', 
		'paymentSubmit' => 'Enviar pago',	
		'reportYearSelect' => 'Seleccione un año',
		'requestReport' => 'Solicitar informe',
		'month' => 'Mes',
		
		//Calendar
		'week' => 'semana',	
		'baseCalendar' => 'Calendario base',
		'calendarUpdate' => 'Calendario base actualizado.',
		'calendarUpdateError' => 'El calendario base no se pudo actualizar.',
		
		'selectProvider' => 'Seleccione proveedor médico' ,
		'selectTreatment' => 'Seleccione un tratamiento solicitado' ,
		'treatmentDuration' => 'Duración del tratamiento' ,
		'minutes' => 'minutos' ,
		//Treatment List
		'treatmentExists' => 'El código de tratamiento ya existe. Modifique el tratamiento existente o cree un nuevo código.' ,
		'addTreatment' => 'Agregar un nuevo tratamiento' ,
		'manageTreatment' => 'Gestionar tratamientos existentes' ,
		'treatmentCode' => 'código de tratamiento' ,
		'treatment' => 'tratamiento' ,
		'treatmentType' => 'categoría de tratamiento' ,
		'charge' => 'costo (decimal)' ,
		'duration' => 'duración (min)' ,
		'treatmentId' => 'ID' ,
		
		
		//Account Manager
		'UserId' => 'Identidad de Usuario',
		'nameFirst' => 'Nombre de Pila',
		'nameLast' => 'Apellido',
		'email' => 'Correo Electrónico',
		'DOB' => 'Fecha de Nacimiento',
		'receptionist' => 'Papel Recepcionista',
		'patientAcctMgr' => 'Administrador de cuentas del paciente',
		'officeMgr' => 'Gerente de Oficina',
		'provider' => 'Proveedor de Servicios Médicos',
		'deaNumber' => 'Número de DEA',
		'deactivate' => 'Desactivar Cuenta',
		'reactivate' => 'Reactivar Cuenta',
		'resetPassword' => 'Restablecer la Contraseña',
		'currentUsers' => 'Usuarios Actuales',
		'formerUsers' => 'Antiguos Usuarios',
		'addUsers' => 'Agregar un nuevo usuario',
		
		//Account Manager Update
		'textInputUpdate1' => 'ajustado a',
		'textInputUpdate2' => 'para',
		'updateFailed' => 'No se realizó ningún cambio. Si el nuevo valor es diferente del valor anterior. Vuelve a cargar la página e inténtalo de nuevo.',
		'checkboxTrue' => 'concedido',
		'checkboxFalse' => 'remoto',
		'deactivated' => ' ha sido desactivado y ya no puede utilizar AMOMS.',
		'reactivated' => ' ha sido reactivado y puede volver a utilizar AMOMS',
		'passwordReset' => 'Se restableció la contraseña para',
		'passwordResetEmailSubject' => 'Restablecimiento de contraseña de AMOMS',
		'passwordResetEmailGreeting' => 'Querido',
		'passwordResetEmailBody' => 'Como se ha solicitado, la contraseña se ha restablecido. El valor actual de la contraseña es',
		'passwordResetEmailBody2' => 'Si no solicitó un restablecimiento de contraseña. Póngase en contacto con su gerente de oficina.',
		
		//Reports
		'callBack' => 'Lista de devolución de llamada',
		'dailyApptReminder' => 'Recordatorios diarios de citas',
		'weeklyApptReminder' => 'Recordatorios de citas semanales',
		'internalReferral' => 'Referencia interna',
		'externalReferral' => 'Referencia externa',
		'medicalExcuse' => 'Declaración de Excusa Médica',
		'30days' => 'Más de 30 días vencidos',
		'60days' => 'Más de 60 días vencidos',
		'90days' => 'Más de 90 días vencidos',
		'120days' => 'Más de 120 días vencidos',
		'profitLoss' => 'Pérdida de Beneficios',
		'billableHours' => 'Horas facturables',
		'weeklyPatientsbyProvider' => 'Pacientes Semanales por Proveedor',
		'weeklyRevenueProvider' => 'Ingresos Semanales por Proveedor',
		'importASPEL' => 'Importar ASPEL',
		'exportASPEL' => 'Exportar ASPEL',		
		
		//Billing
		'invalidApptId' => 'Invalid Appointment Id.',
		'invalidPaymentType' => 'Tipo de pago no válido.',
		'invalidPayor' => 'Payor no válido.',
		'invalidPayment' => 'El pago debe ser un número > 0.',
		'paymentCapped' => 'Pago limitado a cuenta en exceso.',
		'paymentApplied' => 'Pago aplicado a la cita #',
		'acctCreditApplied' => 'Crédito de cuenta aplicado.',	
        'providerId' => 'Proveedor',
      	'diagnoisticCode' => 'Código de diagnóstico',
      	'transDate' => 'Fecha de Transacción',
      	'transType' => 'Tipo de pago',
      	'payment' => 'Monto del pago',
      	'transactionId' => 'ID de Transacción',
		'pastDue' => 'Actualmente tiene un saldo vencido de',
		'pleaseRemit' => 'Este saldo incluye los pagos realizados '.date("j F Y", time() - 60 * 60 * 24).' e incluye los pagos de seguros pendientes. Por favor pase por nuestra clínica o envíe el pago a la dirección que aparece a continuación. Comuníquese con nuestra oficina si tiene alguna pregunta sobre su factura.',
		'currentBalance' => 'Balance restante',
		'apptCharge' => 'Cargo por nombramiento',
		'emailBillingSubject' => 'Notificación de la factura de AMOMS',
		'billSent' => 'Correo electrónico enviado',
		'insuranceSubmit' => 'Presentar al Seguro',
				
		//Reports
		'reportSelectProvider' => 'Seleccione un proveedor para hacer una remisión a',
		'reportReferralMessage' => 'Escriba su mensaje de referencia.',
		'reportInternalReferralSubmit' => 'Correo electrónico - Referencia',
		'reportInternalReferralHeader' => 'Referencia interna para',
		'emailClose' => 'Sinceramente,',
		'referralSent' => '=Tu remisión ha sido enviada.',
		'reportExternalReferralSubmit' => 'Correo electrónico - Referencia',
		'reportExternalReferralHeader' => 'ESolicitud de Referencia Externa - Paciente de AMOMS',
		'AMOMSContact' => "Clínica de AMOMS\r\n1100 South Marietta Pkwy\r\nMarietta, GA 30060\r\nPhone: 470-578-6000",
		'reportReferee' => 'Nombre del Proveedor externo',
		'reportRefereeEmail' => 'Correo electrónico externo del proveedor',		
		'excuseTo' => 'Destinatario',
		'excuseFromDate' => 'Fecha de inicio',
		'excuseToDate' => 'Fecha final',
		'reportMedicalExcuseMessage' => 'Eedite la excusa',
		'standardMedicalExcuse' => 'Esta paciente fue vista en mi oficina hoy, y debe ser liberada del trabajo hasta la fecha indicada.',
		'reportMedicalExcuseSubmit' => 'Ver Disculpa Médica',	
		'to' => 'A',
		'from' => 'De',
		're' => 'Re',
		'no30DayReports' => 'No hay cuentas con más de 30 días de vencimiento.',
		'no60DayReports' => 'No hay cuentas con más de 60 días de vencimiento.',
		'no90DayReports' => 'No hay cuentas con más de 90 días de vencimiento.',
		'no120DayReports' => 'No hay cuentas con más de 120 días de vencimiento.',
		'patientId' => 'ID del paciente',
		'patientNamePreferred' => 'Nombre Preferido',
		'patientPhone' => 'Número de teléfono',
		'patientEmailBill' => 'Enviar factura por correo electrónico',
		'patientLastContactAttempt' => 'Fecha del último contacto',
		'contactNote' => 'Detalles de contacto',
		'billableHours' => 'Horas facturables',		
		'apptReminderEmailSubmit' => 'Enviar correos electrónicos',
		'apptPhoneReminder' => 'notificado de la próxima aplicación por teléfono',
		'apptEmailReminder' => 'notificado de la próxima appt por correo electrónico',
		'apptEmailReminderSubject' => 'Recuerdo de citas de AMOMS',
		'apptEmailReminderBody' => 'Este es un recordatorio de su próxima cita en (con)',
		'apptEmailReminderBody' => 'AMOMS Clinica',
		
	); //endFieldNames Language = spa
	  
	$paymentTypeArray = array(
		'cash' => 'Pago en efectivo' ,
		'check' => 'Pago de cheques' ,
		'creditCard' => 'Pago con tarjeta de crédito' ,		
		'insuranceEstimate' => 'Estimación del seguro' ,		
		'insurancePayment' => 'Pago del seguro' ,
		'insuranceProviderCost' => 'Coste del Proveedor de Seguros' ,
		'acctCredit' => 'Crédito de la cuenta' ,			
	); //end Payment Types Language = spa
	  
	$payorArray = array(
		'self' => 'Yo' ,
		'insurance' => 'Seguro' ,
		'thirdParty' => 'Terceros' ,
	); //end PayorType Language = spa
	  
	$dayArray = array('mon'=>'Lunes' , 'tue'=>'Martes' , 'wed'=>'Miércoles' , 'thu'=>'Jueves' , 'fri'=>'Viernes' , 'sat'=>'Sábado');
	  
  } else {
    $fieldNames = array( 
		'allergies' => 'Allergies' , 
		'newAppt' => 'New Medical Appointment' , 
		'apptDate' => 'Appt Date' , 
        'bloodPressure' => 'Blood Pressure',
		'pulse' => 'Pulse Rate',
		'respiration' => 'Respiratory Rate',
		'height' => 'Height (cm)',
		'weight' => 'Weight (kg)',
		'reason' => 'Complaint',
		'diagnosis' => 'Diagnosis',
		'notes' => 'Notes',	
		'treatment' => 'Treatment' ,
		'followup' => 'Please enter the number of weeks until next visit.',
		'nextAppt' => 'Next Appt' ,
		'followupApptSet' => 'Followup appointment requested for the week of',		
		'diagnosticCode' => 'Diagnostic Code',
		'namePreferred' => 'Preferred Name',
		'callBackDate' => 'Call Back Date',
		'phone' => 'Phone Number',
		
		
		//Billing
		'acctBalance' => 'Account Balance' ,
		'acctStatus' => 'Account Status' ,
		'paymentAmount' => 'Payment Amount' ,
		'paymentType' => 'Payment Method',
		'payor' => 'Payor', 
		'paymentSubmit' => 'Submit Payment',	
		
		 //Calendar
		 'week' => 'week',	
		 'baseCalendar' => 'Base Calendar',	
		 'calendarUpdate' => 'Base Calendar Updated',
		 'calendarUpdateError' => 'Base Calendar could not be updated',
		'selectProvider' => 'Select Medical Provider' ,
		'selectTreatment' => 'Select Requested Treatment' ,
		'treatmentDuration' => 'Treatment Duration' ,
		'minutes' => 'minutes' ,
		
		//Treatment List
		'treatmentExists' => 'The treatment code already exists. Please modify the existing treatment or create a new code.' ,
		'addTreatment' => 'Add a new treatment' ,
		'manageTreatment' => 'Manage existing treatments' ,
		'treatmentCode' => 'treatment code' ,
		'treatment' => 'treatment' ,
		'treatmentType' => 'treatment type' ,
		'charge' => 'charge (decimal)' ,
		'duration' => 'duration(min)' ,
		'treatmentId' => 'ID' ,
		
		
		
		//Account Manager
		'UserId' => 'User ID',
		'nameFirst' => 'First Name',
		'nameLast' => 'Last Name',
		'email' => 'Email Address',
		'DOB' => 'Date of Birth',
		'receptionist' => 'Receptionist Role',
		'patientAcctMgr' => 'Patient Acct Mgr Role',
		'officeMgr' => 'Office Mgr Role',
		'provider' => 'Provider Role',
		'deaNumber' => 'Provider DEA Number',
		'deactivate' => 'Deactivate Acct',
		'reactivate' => 'Reactivate Acct',
		'resetPassword' => 'Reset Password',
		'currentUsers' => 'Current Users',
		'formerUsers' => 'Former Users',
		'addUsers' => 'Add a new user',
		
		//Account Manager Update
		'textInputUpdate1' => 'set to',
		'textInputUpdate2' => 'for',
		'updateFailed' => 'No change made. If the new value is different from the old value.  Please reload the page and try again.',
		'checkboxTrue' => 'granted',
		'checkboxFalse' => 'removed',
		'deactivated' => ' has been deactivated and can no longer use AMOMS.',
		'reactivated' => ' has been reactivated and can again use AMOMS',
		'passwordReset' => 'Password has been reset for',
		'passwordResetEmailSubject' => 'AMOMS Password Reset',
		'passwordResetEmailGreeting' => 'Dear',
		'passwordResetEmailBody' => 'As requested your password has been reset.  The current value of the password is ',
		'passwordResetEmailBody2' => 'If you did not request a password reset. Please contact your office manager.',
		
		//Reports
		'callBack' => 'Call Back List',
		'dailyApptReminder' => 'Daily Appointment Reminders',
		'weeklyApptReminder' => 'Weekly Appointment Reminders',
		'internalReferral' => 'Internal Referral',
		'externalReferral' => 'External Referral',
		'medicalExcuse' => 'Medical Excuse Statement',
		'30days' => '30+ days past due',
		'60days' => '60+ days past due',
		'90days' => '90+ days past due',
		'120days' => '120+ days past due',
		'profitLoss' => 'Profit / Loss',
		'billableHours' => 'Billable Hours',
		'weeklyPatientsbyProvider' => 'Weekly Patients by Provider',
		'weeklyRevenueProvider' => 'Weekly Revenue by Provider',
		'importASPEL' => 'Import ASPEL',
		'exportASPEL' => 'Export ASPEL',
		//Billing
		'invalidApptId' => 'Invalid Appointment Id.',
		'invalidPaymentType' => 'Invalid Payment Type.',
		'invalidPayor' => 'Invalid Payor.',
		'invalidPayment' => 'Payment must be a number > 0.',
		'paymentCapped' => 'Payment capped to account overpayment.',
		'paymentApplied' => 'payment applied to appointment #',
		'acctCreditApplied' => 'account credit applied.',
        'providerId' => 'Provider',
      	'diagnoisticCode' => 'Diagnostic Code',
      	'transDate' => 'Transaction Date',
      	'transType' => 'Payment Type',
      	'payment' => 'Payment Amount',
      	'transactionId' => 'Transaction ID',
		'pastDue' => 'You currently have a past due balance of',
		'pleaseRemit' => 'This balance includes payments made through '.date("j F Y", time() - 60 * 60 * 24).' and includes pending insurance payments.  Please stop by the our clinic or remit payment to the address below. Please contact our office if you have any questions about your bill.',
		'currentBalance' => 'Remaining Balance',
		'apptCharge' => 'Appointment Charge',
		'emailBillingSubject' => 'AMOMS Bill Notification',
		'billSent' => 'Email Bill Sent',
		'insuranceSubmit' => 'Submit to Insurance',
		
		//Reports
		'reportSelectProvider' => 'Select a provider to make a referral to',
		'reportReferralMessage' => 'Type your message.',
		'reportInternalReferralSubmit' => 'Email Referral',
		'reportInternalReferralHeader' => 'Internal Referral for ',
		'emailClose' => 'Sincerely,',
		'referralSent' => 'Your referral has been sent.',
		'reportExternalReferralSubmit' => 'Email Referral',
		'reportExternalReferralHeader' => 'External Referral Request - AMOMS Patient ',
		'AMOMSContact' => "AMOMS Clinic\r\n1100 South Marietta Pkwy\r\nMarietta, GA 30060\r\nPhone: 470-578-6000",
		'reportReferee' => 'External Provider Name',
		'reportRefereeEmail' => 'External Provider Email',
		'excuseTo' => 'Addressee',
		'excuseFromDate' => 'Start Date',
		'excuseToDate' => 'End Date',
		'reportMedicalExcuseMessage' => 'Edit the excuse',
		'standardMedicalExcuse' => 'This patient was seen in my office today, and should be release from work until the date indicated.',
		'reportMedicalExcuseSubmit' => 'View Excuse',		
		'to' => 'To',
		'from' => 'From',
		'no30DayReports' => 'No accounts 30+ days past due',
		'no60DayReports' => 'No accounts 60+ days past due',
		'no90DayReports' => 'No accounts 90+ days past due',
		'no120DayReports' => 'No accounts 120+ days past due',
		'patientId' => 'Patient ID',
		'patientNamePreferred' => 'Preferred Name',
		'patientPhone' => 'Phone Number',
		'patientEmailBill' => 'Send bill via email',
		'patientLastContactAttempt' => 'Date of Last Contact',
		'contactNote' => 'Contact Details',
		'reportYearSelect' => 'Please select a year',
		'requestReport' => 'Request Report',
		'month' => 'Month',
		'billableHours' => 'Billable Hours',
		'apptReminderEmailSubmit' => 'Send Appointment Reminder Email',
		'apptPhoneReminder' => 'notified of upcoming appt by phone',
		'apptEmailReminder' => 'notified of upcoming appt by email',
		'apptEmailReminderSubject' => 'AMOMS Appointment Reminder',
		'apptEmailReminderBody' => 'This is a reminder of your upcoming appointment at (with)',
		'apptEmailReminderBody2' => 'AMOMS Clinic',
		
	); //endFieldNames Language = default (eng)
	$paymentTypeArray = array(
		'cash' => 'Cash' ,
		'check' => 'Check' ,
		'creditCard' => 'Credit Card' ,		
		'insuranceEstimate' => 'Insurance Estimate' ,		
		'insurancePayment' => 'Insurance Payment' ,
		'insuranceProviderCost' => 'Insurance Provider Cost' ,
		'acctCredit' => 'Apply Account Credit' ,			
	);   //end Payment Types Language = default (eng)
	$payorArray = array(
		'self' => 'Self' ,
		'insurance' => 'Insurance' ,
		'thirdParty' => 'Third party' ,				
	); //end PayorType Language = default (eng)  
    $dayArray = array('mon'=>'Monday' , 'tue'=>'Tuesday' , 'wed'=>'Wednesday' , 'thu'=>'Thursday' , 'fri'=>'Friday' , 'sat'=>'Saturday');
  }
