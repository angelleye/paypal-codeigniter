<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Adaptive_payments extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();
		
		// Load helpers
		$this->load->helper('url');
		
		// Load PayPal library
		$this->config->load('paypal');
		
		$config = array(
			'Sandbox' => $this->config->item('Sandbox'), 			// Sandbox / testing mode option.
			'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
			'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
			'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
			'APISubject' => '', 									// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
			'APIVersion' => $this->config->item('APIVersion'), 		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
			'DeviceID' => $this->config->item('DeviceID'), 
			'ApplicationID' => $this->config->item('ApplicationID'), 
			'DeveloperEmailAccount' => $this->config->item('DeveloperEmailAccount')
		);
		
		if($config['Sandbox'])
		{
			// Show Errors
			error_reporting(E_ALL);
			ini_set('display_errors', '1');	
		}
		
		$this->load->library('paypal/Paypal_adaptive', $config);	
	}
	
	
	function index()
	{
		$this->load->view('adaptive_payments_demo');
	}
	
	function Pay()
	{
		// Prepare request arrays
		$PayRequestFields = array(
								'ActionType' => '', 								// Required.  Whether the request pays the receiver or whether the request is set up to create a payment request, but not fulfill the payment until the ExecutePayment is called.  Values are:  PAY, CREATE, PAY_PRIMARY
								'CancelURL' => '', 									// Required.  The URL to which the sender's browser is redirected if the sender cancels the approval for the payment after logging in to paypal.com.  1024 char max.
								'CurrencyCode' => '', 								// Required.  3 character currency code.
								'FeesPayer' => '', 									// The payer of the fees.  Values are:  SENDER, PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
								'IPNNotificationURL' => '', 						// The URL to which you want all IPN messages for this payment to be sent.  1024 char max.
								'Memo' => '', 										// A note associated with the payment (text, not HTML).  1000 char max
								'Pin' => '', 										// The sener's personal id number, which was specified when the sender signed up for the preapproval
								'PreapprovalKey' => '', 							// The key associated with a preapproval for this payment.  The preapproval is required if this is a preapproved payment.  
								'ReturnURL' => '', 									// Required.  The URL to which the sener's browser is redirected after approvaing a payment on paypal.com.  1024 char max.
								'ReverseAllParallelPaymentsOnError' => '', 			// Whether to reverse paralel payments if an error occurs with a payment.  Values are:  TRUE, FALSE
								'SenderEmail' => '', 								// Sender's email address.  127 char max.
								'TrackingID' => ''									// Unique ID that you specify to track the payment.  127 char max.
								);
								
		$ClientDetailsFields = array(
								'CustomerID' => '', 								// Your ID for the sender  127 char max.
								'CustomerType' => '', 								// Your ID of the type of customer.  127 char max.
								'GeoLocation' => '', 								// Sender's geographic location
								'Model' => '', 										// A sub-identification of the application.  127 char max.
								'PartnerName' => ''									// Your organization's name or ID
								);
								
		$FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');
		
		$Receivers = array();
		$Receiver = array(
						'Amount' => '', 											// Required.  Amount to be paid to the receiver.
						'Email' => '', 												// Receiver's email address. 127 char max.
						'InvoiceID' => '', 											// The invoice number for the payment.  127 char max.
						'PaymentType' => '', 										// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
						'PaymentSubType' => '', 									// The transaction subtype for the payment.
						'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''), // Receiver's phone number.   Numbers only.
						'Primary' => ''												// Whether this receiver is the primary receiver.  Values are:  TRUE, FALSE
						);
		array_push($Receivers,$Receiver);
		
		$SenderIdentifierFields = array(
										'UseCredentials' => ''						// If TRUE, use credentials to identify the sender.  Default is false.
										);
										
		$AccountIdentifierFields = array(
										'Email' => '', 								// Sender's email address.  127 char max.
										'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => '')								// Sender's phone number.  Numbers only.
										);
										
		$PayPalRequestData = array(
							'PayRequestFields' => $PayRequestFields, 
							'ClientDetailsFields' => $ClientDetailsFields, 
							'FundingTypes' => $FundingTypes, 
							'Receivers' => $Receivers, 
							'SenderIdentifierFields' => $SenderIdentifierFields, 
							'AccountIdentifierFields' => $AccountIdentifierFields
							);	
							
		$PayPalResult = $this->paypal_adaptive->Pay($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			echo '<pre />';
			print_r($PayPalResult);
		}
	}
	
	
	function Pay_with_options()
	{
		// Pay
		$PayRequestFields = array(
				'CancelURL' => '', 									// Required.  The URL to which the sender's browser is redirected if the sender cancels the approval for the payment after logging in to paypal.com.  1024 char max.
				'CurrencyCode' => '', 								// Required.  3 character currency code.
				'FeesPayer' => '', 									// The payer of the fees.  Values are:  SENDER, PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
				'IPNNotificationURL' => '', 						// The URL to which you want all IPN messages for this payment to be sent.  1024 char max.
				'Memo' => '', 										// A note associated with the payment (text, not HTML).  1000 char max
				'Pin' => '', 										// The sener's personal id number, which was specified when the sender signed up for the preapproval
				'PreapprovalKey' => '', 							// The key associated with a preapproval for this payment.  The preapproval is required if this is a preapproved payment.
				'ReturnURL' => '', 									// Required.  The URL to which the sener's browser is redirected after approvaing a payment on paypal.com.  1024 char max.
				'ReverseAllParallelPaymentsOnError' => '', 			// Whether to reverse paralel payments if an error occurs with a payment.  Values are:  TRUE, FALSE
				'SenderEmail' => '', 								// Sender's email address.  127 char max.
				'TrackingID' => ''									// Unique ID that you specify to track the payment.  127 char max.
		);
		
		$ClientDetailsFields = array(
				'CustomerID' => '', 								// Your ID for the sender  127 char max.
				'CustomerType' => '', 								// Your ID of the type of customer.  127 char max.
				'GeoLocation' => '', 								// Sender's geographic location
				'Model' => '', 										// A sub-identification of the application.  127 char max.
				'PartnerName' => ''									// Your organization's name or ID
		);
		
		$FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');					// Funding constrainigs require advanced permissions levels.
		
		$Receivers = array();
		$Receiver = array(
				'Amount' => '', 											// Required.  Amount to be paid to the receiver.
				'Email' => '', 												// Receiver's email address. 127 char max.
				'InvoiceID' => '', 											// The invoice number for the payment.  127 char max.
				'PaymentType' => '', 										// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
				'PaymentSubType' => '', 									// The transaction subtype for the payment.
				'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''), // Receiver's phone number.   Numbers only.
				'Primary' => ''												// Whether this receiver is the primary receiver.  Values are boolean:  TRUE, FALSE
		);
		array_push($Receivers,$Receiver);
		
		$SenderIdentifierFields = array(
				'UseCredentials' => ''						// If TRUE, use credentials to identify the sender.  Default is false.
		);
		
		$AccountIdentifierFields = array(
				'Email' => '', 								// Sender's email address.  127 char max.
				'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => '')								// Sender's phone number.  Numbers only.
		);
		
		// SetPaymentOptions
		$SPOFields = array(
				'PayKey' => '', 							// Required.  The pay key that identifies the payment for which you want to set payment options.
				'ShippingAddressID' => '' 					// Sender's shipping address ID.
		);
		
		$DisplayOptions = array(
				'EmailHeaderImageURL' => '', 			// The URL of the image that displays in the header of customer emails.  1,024 char max.  Image dimensions:  43 x 240
				'EmailMarketingImageURL' => '', 		// The URL of the image that displays in the customer emails.  1,024 char max.  Image dimensions:  80 x 530
				'HeaderImageURL' => '', 				// The URL of the image that displays in the header of a payment page.  1,024 char max.  Image dimensions:  750 x 90
				'BusinessName' => ''					// The business name to display.  128 char max.
		);
		
		$InstitutionCustomer = array(
				'CountryCode' => '', 				// Required.  2 char code of the home country of the end user.
				'DisplayName' => '', 				// Required.  The full name of the consumer as known by the institution.  200 char max.
				'InstitutionCustomerEmail' => '', 	// The email address of the consumer.  127 char max.
				'FirstName' => '', 					// Required.  The first name of the consumer.  64 char max.
				'LastName' => '', 					// Required.  The last name of the consumer.  64 char max.
				'InstitutionCustomerID' => '', 		// Required.  The unique ID assigned to the consumer by the institution.  64 char max.
				'InstitutionID' => ''				// Required.  The unique ID assiend to the institution.  64 char max.
		);
		
		$SenderOptions = array(
				'RequireShippingAddressSelection' => '' // Boolean.  If true, require the sender to select a shipping address during the embedded payment flow.  Default is false.
		);
		
		// Begin loop to populate receiver options.
		$ReceiverOptions = array();
		$ReceiverOption = array(
				'Description' => '', 					// A description you want to associate with the payment.  1000 char max.
				'CustomID' => '' 						// An external reference number you want to associate with the payment.  1000 char max.
		);
		
		$InvoiceData = array(
				'TotalTax' => '', 							// Total tax associated with the payment.
				'TotalShipping' => '' 						// Total shipping associated with the payment.
		);
		
		$InvoiceItems = array();
		$InvoiceItem = array(
				'Name' => '', 								// Name of item.
				'Identifier' => '', 						// External reference to item or item ID.
				'Price' => '', 								// Total of line item.
				'ItemPrice' => '',							// Price of an individual item.
				'ItemCount' => ''							// Item QTY
		);
		array_push($InvoiceItems,$InvoiceItem);
		
		$ReceiverIdentifier = array(
				'Email' => '', 						// Receiver's email address.  127 char max.
				'PhoneCountryCode' => '', 			// Receiver's telephone number country code.
				'PhoneNumber' => '', 				// Receiver's telephone number.
				'PhoneExtension' => ''				// Receiver's telephone extension.
		);
		
		$ReceiverOption['InvoiceData'] = $InvoiceData;
		$ReceiverOption['InvoiceItems'] = $InvoiceItems;
		$ReceiverOption['ReceiverIdentifier'] = $ReceiverIdentifier;
		array_push($ReceiverOptions,$ReceiverOption);
		// End receiver options loop
		
		$PayPalRequestData = array(
				'PayRequestFields' => $PayRequestFields,
				'ClientDetailsFields' => $ClientDetailsFields,
				'FundingTypes' => $FundingTypes,
				'Receivers' => $Receivers,
				'SenderIdentifierFields' => $SenderIdentifierFields,
				'AccountIdentifierFields' => $AccountIdentifierFields,
				'SPOFields' => $SPOFields,
				'DisplayOptions' => $DisplayOptions,
				'InstitutionCustomer' => $InstitutionCustomer,
				'SenderOptions' => $SenderOptions,
				'ReceiverOptions' => $ReceiverOptions
		);
		
		
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $this->paypal_adaptive->PayWithOptions($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			echo '<pre />';
			print_r($PayPalResult);
		}
	}
	
	
	function Pay_chained_demo()
	{
		// Prepare request arrays
		$PayRequestFields = array(
								'ActionType' => 'PAY', 								// Required.  Whether the request pays the receiver or whether the request is set up to create a payment request, but not fulfill the payment until the ExecutePayment is called.  Values are:  PAY, CREATE, PAY_PRIMARY
								'CancelURL' => site_url('paypal/adaptive_payments/pay_cancel'), 									// Required.  The URL to which the sender's browser is redirected if the sender cancels the approval for the payment after logging in to paypal.com.  1024 char max.
								'CurrencyCode' => 'USD', 								// Required.  3 character currency code.
								'FeesPayer' => 'EACHRECEIVER', 									// The payer of the fees.  Values are:  SENDER, PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
								'IPNNotificationURL' => '', 						// The URL to which you want all IPN messages for this payment to be sent.  1024 char max.
								'Memo' => '', 										// A note associated with the payment (text, not HTML).  1000 char max
								'Pin' => '', 										// The sener's personal id number, which was specified when the sender signed up for the preapproval
								'PreapprovalKey' => '', 							// The key associated with a preapproval for this payment.  The preapproval is required if this is a preapproved payment.  
								'ReturnURL' => site_url('paypal/adaptive_payments/pay_return'), 									// Required.  The URL to which the sener's browser is redirected after approvaing a payment on paypal.com.  1024 char max.
								'ReverseAllParallelPaymentsOnError' => '', 			// Whether to reverse paralel payments if an error occurs with a payment.  Values are:  TRUE, FALSE
								'SenderEmail' => '', 								// Sender's email address.  127 char max.
								'TrackingID' => ''									// Unique ID that you specify to track the payment.  127 char max.
								);
								
		$ClientDetailsFields = array(
								'CustomerID' => '', 								// Your ID for the sender  127 char max.
								'CustomerType' => '', 								// Your ID of the type of customer.  127 char max.
								'GeoLocation' => '', 								// Sender's geographic location
								'Model' => '', 										// A sub-identification of the application.  127 char max.
								'PartnerName' => ''									// Your organization's name or ID
								);
								
		$FundingTypes = array('ECHECK', 'BALANCE', 'CREDITCARD');
		
		$Receivers = array();
		$Receiver = array(
						'Amount' => '100.00', 											// Required.  Amount to be paid to the receiver.
						'Email' => 'agb_b_1296836857_per@angelleye.com', 												// Receiver's email address. 127 char max.
						'InvoiceID' => '123-ABCDEF', 											// The invoice number for the payment.  127 char max.
						'PaymentType' => 'SERVICE', 										// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
						'PaymentSubType' => '', 									// The transaction subtype for the payment.
						'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''), // Receiver's phone number.   Numbers only.
						'Primary' => 'true'												// Whether this receiver is the primary receiver.  Values are boolean:  TRUE, FALSE
						);
		array_push($Receivers,$Receiver);
		
		$Receiver = array(
						'Amount' => '10.00', 											// Required.  Amount to be paid to the receiver.
						'Email' => 'agbc_1296755893_biz@angelleye.com', 												// Receiver's email address. 127 char max.
						'InvoiceID' => '123-ABCDEF', 											// The invoice number for the payment.  127 char max.
						'PaymentType' => 'SERVICE', 										// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
						'PaymentSubType' => '', 									// The transaction subtype for the payment.
						'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''), // Receiver's phone number.   Numbers only.
						'Primary' => 'false'												// Whether this receiver is the primary receiver.  Values are boolean:  TRUE, FALSE
						);
		array_push($Receivers,$Receiver);
		
		$Receiver = array(
						'Amount' => '10.00', 											// Required.  Amount to be paid to the receiver.
						'Email' => 'agb_1296755685_biz@angelleye.com', 												// Receiver's email address. 127 char max.
						'InvoiceID' => '123-ABCDEF', 											// The invoice number for the payment.  127 char max.
						'PaymentType' => 'SERVICE', 										// Transaction type.  Values are:  GOODS, SERVICE, PERSONAL, CASHADVANCE, DIGITALGOODS
						'PaymentSubType' => '', 									// The transaction subtype for the payment.
						'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => ''), // Receiver's phone number.   Numbers only.
						'Primary' => 'false'												// Whether this receiver is the primary receiver.  Values are boolean:  TRUE, FALSE
						);
		array_push($Receivers,$Receiver);
		
		$SenderIdentifierFields = array(
										'UseCredentials' => ''						// If TRUE, use credentials to identify the sender.  Default is false.
										);
										
		$AccountIdentifierFields = array(
										'Email' => '', 								// Sender's email address.  127 char max.
										'Phone' => array('CountryCode' => '', 'PhoneNumber' => '', 'Extension' => '')								// Sender's phone number.  Numbers only.
										);
										
		$PayPalRequestData = array(
							'PayRequestFields' => $PayRequestFields, 
							'ClientDetailsFields' => $ClientDetailsFields, 
							'FundingTypes' => $FundingTypes, 
							'Receivers' => $Receivers, 
							'SenderIdentifierFields' => $SenderIdentifierFields, 
							'AccountIdentifierFields' => $AccountIdentifierFields
							);	
							
		$PayPalResult = $this->paypal_adaptive->Pay($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			header('Location: '.$PayPalResult['RedirectURL']);
			exit();
		}
	}
	
	
	function Payment_details()
	{
		// Prepare request arrays
		$PaymentDetailsFields = array(
									'PayKey' => '', 							// The pay key that identifies the payment for which you want to retrieve details.  
									'TransactionID' => '', 						// The PayPal transaction ID associated with the payment.  
									'TrackingID' => ''							// The tracking ID that was specified for this payment in the PayRequest message.  127 char max.
									);
									
		$PayPalRequestData = array('PaymentDetailsFields' => $PaymentDetailsFields);
		$PayPalResult = $this->paypal_adaptive->PaymentDetails($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Execute_payment()
	{
		// Prepare request arrays
		$ExecutePaymentFields = array(
									'PayKey' => '', 								// The pay key that identifies the payment to be executed.  This is the key returned in the PayResponse message.
									'FundingPlanID' => '' 							// The ID of the funding plan from which to make this payment.
									);
									
		$PayPalRequestData = array('ExecutePaymentFields' => $ExecutePaymentFields);	
		$PayPalResult = $this->paypal_adaptive->ExecutePayment($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_payment_options()
	{
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayKey = '';
		$PayPalResult = $this->paypal_adaptive->GetPaymentOptions($PayKey);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Set_payment_options()
	{
		// Prepare request arrays
		$SPOFields = array(
						'PayKey' => '', 							// Required.  The pay key that identifies the payment for which you want to set payment options.  
						'ShippingAddressID' => '' 					// Sender's shipping address ID.
						);
						
		$DisplayOptions = array(
						'EmailHeaderImageURL' => '', 			// The URL of the image that displays in the header of customer emails.  1,024 char max.  Image dimensions:  43 x 240
						'EmailMarketingImageURL' => '', 		// The URL of the image that displays in the customer emails.  1,024 char max.  Image dimensions:  80 x 530
						'HeaderImageURL' => '', 				// The URL of the image that displays in the header of a payment page.  1,024 char max.  Image dimensions:  750 x 90
						'BusinessName' => ''					// The business name to display.  128 char max.
						);
								
		$InstitutionCustomer = array(
						'CountryCode' => '', 				// Required.  2 char code of the home country of the end user.
						'DisplayName' => '', 				// Required.  The full name of the consumer as known by the institution.  200 char max.
						'InstitutionCustomerEmail' => '', 	// The email address of the consumer.  127 char max.
						'FirstName' => '', 					// Required.  The first name of the consumer.  64 char max.
						'LastName' => '', 					// Required.  The last name of the consumer.  64 char max.
						'InstitutionCustomerID' => '', 		// Required.  The unique ID assigned to the consumer by the institution.  64 char max.
						'InstitutionID' => ''				// Required.  The unique ID assiend to the institution.  64 char max.
						);
								
		$SenderOptions = array(
						'RequireShippingAddressSelection' => '' // Boolean.  If true, require the sender to select a shipping address during the embedded payment flow.  Default is false.
						);
							
		// Begin loop to populate receiver options.
		$ReceiverOptions = array();
		$ReceiverOption = array(
				'Description' => '', 					// A description you want to associate with the payment.  1000 char max.
				'CustomID' => '' 						// An external reference number you want to associate with the payment.  1000 char max.
		);
			
		$InvoiceData = array(
				'TotalTax' => '', 							// Total tax associated with the payment.
				'TotalShipping' => '' 						// Total shipping associated with the payment.
		);
		
		$InvoiceItems = array();
		$InvoiceItem = array(
				'Name' => '', 								// Name of item.
				'Identifier' => '', 						// External reference to item or item ID.
				'Price' => '', 								// Total of line item.
				'ItemPrice' => '',							// Price of an individual item.
				'ItemCount' => ''							// Item QTY
		);
		array_push($InvoiceItems,$InvoiceItem);
		
		$ReceiverIdentifier = array(
				'Email' => '', 						// Receiver's email address.  127 char max.
				'PhoneCountryCode' => '', 			// Receiver's telephone number country code.
				'PhoneNumber' => '', 				// Receiver's telephone number.
				'PhoneExtension' => ''				// Receiver's telephone extension.
		);
		
		$ReceiverOption['InvoiceData'] = $InvoiceData;
		$ReceiverOption['InvoiceItems'] = $InvoiceItems;
		$ReceiverOption['ReceiverIdentifier'] = $ReceiverIdentifier;
		array_push($ReceiverOptions,$ReceiverOption);
		// End receiver options loop
		
		$PayPalRequestData = array(
						'SPOFields' => $SPOFields, 
						'DisplayOptions' => $DisplayOptions, 
						'InstitutionCustomer' => $InstitutionCustomer, 
						'SenderOptions' => $SenderOptions, 
						'ReceiverOptions' => $ReceiverOptions
						);
		
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $this->paypal_adaptive->SetPaymentOptions($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
		}	
	}
	
	
	function Preapproval()
	{
		// Prepare request arrays
		$PreapprovalFields = array(
								   'CancelURL' => '',  								// Required.  URL to send the browser to after the user cancels.
								   'CurrencyCode' => '', 							// Required.  Currency Code.
								   'DateOfMonth' => '', 							// The day of the month on which a monthly payment is to be made.  0 - 31.  Specifying 0 indiciates that payment can be made on any day of the month.
								   'DayOfWeek' => '', 								// The day of the week that a weekly payment should be made.  Allowable values: NO_DAY_SPECIFIED, SUNDAY, MONDAY, TUESDAY, WEDNESDAY, THURSDAY, FRIDAY, SATURDAY
								   'EndingDate' => '', 								// Required.  The last date for which the preapproval is valid.  It cannot be later than one year from the starting date.
								   'IPNNotificationURL' => '', 						// The URL for IPN notifications.
								   'MaxAmountPerPayment' => '', 					// The preapproved maximum amount per payment.  Cannot exceed the preapproved max total amount of all payments.
								   'MaxNumberOfPayments' => '', 					// The preapproved maximum number of payments.  Cannot exceed the preapproved max total number of all payments. 
								   'MaxTotalAmountOfPaymentsPerPeriod' => '', 	// The preapproved maximum number of all payments per period.
								   'MaxTotalAmountOfAllPayments' => '', 			// The preapproved maximum total amount of all payments.  Cannot exceed $2,000 USD or the equivalent in other currencies.
								   'Memo' => '', 									// A note about the preapproval.
								   'PaymentPeriod' => '', 							// The pament period.  One of the following:  NO_PERIOD_SPECIFIED, DAILY, WEEKLY, BIWEEKLY, SEMIMONTHLY, MONTHLY, ANNUALLY
								   'PinType' => '', 								// Whether a personal identification number is required.  It is one of the following:  NOT_REQUIRED, REQUIRED
								   'ReturnURL' => '', 								// URL to return the sender to after approving at PayPal.
								   'SenderEmail' => '', 							// Sender's email address.  If not specified, the email address of the sender who logs on to approve is used.
								   'StartingDate' => '', 							// Required.  First date for which the preapproval is valid.  Cannot be before today's date or after the ending date.
								   'FeesPayer' => '', 								// The payer of the PayPal fees.  Values are:  SENDER, PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY
								   'DisplayMaxTotalAmount' => ''					// Whether to display the max total amount of this preapproval.  Values are:  true/false
								   );
		
		$ClientDetailsFields = array(
									 'CustomerID' => '', 						// Your ID for the sender.
									 'CustomerType' => '', 						// Your ID of the type of customer.
									 'GeoLocation' => '', 						// Sender's geographic location.
									 'Model' => '', 							// A sub-id of the application
									 'PartnerName' => ''						// Your organization's name or ID.
									 );
		
		$PayPalRequestData = array(
							 'PreapprovalFields' => $PreapprovalFields, 
							 'ClientDetailsFields' => $ClientDetailsFields
							 );	
		
		$PayPalResult = $this->paypal_adaptive->Preapproval($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Preapproval_details()
	{
		// Prepare request arrays
		$PreapprovalDetailsFields = array(
										  'GetBillingAddress' => '', 									// Opion to get the billing address in the response.  true or false.  Only available with Advanced permissions levels.
										  'PreapprovalKey' => '' 										// Required.  A preapproval key that identifies the preapproval for which you want to retrieve details.  Returned in the PreapprovalResponse
										  );
		
		$PayPalRequestData = array('PreapprovalDetailsFields' => $PreapprovalDetailsFields);
		$PayPalResult = $this->paypal_adaptive->PreapprovalDetails($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Cancel_preapproval()
	{
		// Prepare request arrays
		$CancelPreapprovalFields = array(
										 'PreapprovalKey' => ''										// Required.  Preapproval key that identifies the preapproval to be canceled.
										 );
		
		$PayPalRequestData = array('CancelPreapprovalFields' => $CancelPreapprovalFields);
		$PayPalResult = $this->paypal_adaptive->CancelPreapproval($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Refund()
	{
		// Prepare request arrays
		$RefundFields = array(
							  'CurrencyCode' => '', 											// Required.  Must specify code used for original payment.  You do not need to specify if you use a payKey to refund a completed transaction.
							  'PayKey' => '',  													// Required.  The key used to create the payment that you want to refund.
							  'TransactionID' => '', 											// Required.  The PayPal transaction ID associated with the payment that you want to refund.
							  'TrackingID' => ''												// Required.  The tracking ID associated with the payment that you want to refund.
							  );
		
		$Receivers = array();
		$Receiver = array(
						  'Email' => '',									// A receiver's email address. 
						  'Amount' => '', 									// Amount to be debited to the receiver's account.
						  'Primary' => '', 									// Set to true to indicate a chained payment.  Only one receiver can be a primary receiver.  Omit this field, or set to false for simple and parallel payments.
						  'InvoiceID' => '', 								// The invoice number for the payment.  This field is only used in Pay API operation.
						  'PaymentType' => ''								// The transaction subtype for the payment.  Allowable values are: GOODS, SERVICE
						  );
		
		array_push($Receivers, $Receiver);
		
		$PayPalRequestData = array(
							 'RefundFields' => $RefundFields, 
							 'Receivers' => $Receivers
							 );	
							 
		$PayPalResult = $this->paypal_adaptive->Refund($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Convert_currency()
	{
		// Prepare request arrays
		$BaseAmountList = array();
		$BaseAmountData = array(
								'Code' => 'USD', 						// Currency code.
								'Amount' => '100.00'						// Amount to be converted.
								);
		array_push($BaseAmountList, $BaseAmountData);
		
		$ConvertToCurrencyList = array('BRL', 'AUD', 'CAD');			// Currency Codes
		
		$PayPalRequestData = array(
								'BaseAmountList' => $BaseAmountList, 
								'ConvertToCurrencyList' => $ConvertToCurrencyList
								);	
								
		$PayPalResult = $this->paypal_adaptive->ConvertCurrency($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('convert_currency',$data);
		}	
	}
	
	
	function Create_account()
	{
		// Prepare request arrays
		$CreateAccountFields = array(
									 'AccountType' => '',  										// Required.  The type of account to be created.  Personal or Premier
									 'CitizenshipCountryCode' => '',  							// Required.  The code of the country to be associated with the business account.  This field does not apply to personal or premier accounts.
									 'ContactPhoneNumber' => '', 								// Required.  The phone number associated with the new account.
									 'HomePhoneNumber' => '', 									// Home phone number associated with the account.
									 'MobilePhoneNumber' => '', 								// Mobile phone number associated with the account.
									 'ReturnURL' => '', 										// Required.  URL to redirect the user to after leaving PayPal pages.
									 'ShowAddCreditCard' => '', 								// Whether or not to show the Add Credit Card option.  Values:  true/false
									 'ShowMobileConfirm' => '', 								// Whether or not to show the mobile confirmation option.  Values:  true/false 
									 'ReturnURLDescription' => '', 								// A description of the Return URL.
									 'UseMiniBrowser' => '', 									// Whether or not to use the minibrowser flow.  Values:  true/false  Note:  If you specify true here, do not specify values for ReturnURL or ReturnURLDescription
									 'CurrencyCode' => '', 										// Required.  Currency code associated with the new account.  
									 'DateOfBirth' => '', 										// Date of birth of the account holder.  YYYY-MM-DDZ format.  For example, 1970-01-01Z
									 'EmailAddress' => '', 										// Required.  Email address.
									 'Saluation' => '', 										// A saluation for the account holder.
									 'FirstName' => '', 										// Required.  First name of the account holder.
									 'MiddleName' => '', 										// Middle name of the account holder.
									 'LastName' => '', 											// Required.  Last name of the account holder.
									 'Suffix' => '',  											// Suffix name for the account holder.
									 'NotificationURL' => '', 									// URL for IPN
									 'PreferredLanguageCode' => '', 							// Required.  The code indicating the language to be associated with the new account.
									 'RegistrationType' => '', 									// Required.  Whether the PayPal user will use a mobile device or the web to complete registration.  This determins whether a key or a URL is returned for the redirect URL.  Allowable values are:  Web
									 'SuppressWelcomeEmail' => '', 								// Whether or not to suppress the PayPal welcome email.  Values:  true/false
									 'PerformExtraVettingOnThisAccount' => '', 					// Whether to subject the account to extra vetting by PayPal before the account can be used.  Values:  true/false
									 'TaxID' => ''												// Tax ID equivalent to US SSN number.   Note:  Currently only supported in Brazil, which uses tax ID numbers such as CPF and CNPJ.
									);
									
		$BusinessInfo = array(
								'AverageMonthlyVolume' => '', 									// Required.  The avg. monthly transaction volume of the business for which the PayPal account is being created.  Required for all countries except Japan and Australia, and should not be specified for these countries.
								'AveragePrice' => '', 											// Required.  The avg. price per transaction.  Required for all countries except Japan and Australia, and should not be specified for these countries.
								'BusinessName' => '', 											// Required.  The name of the business for which the PayPal account is being created. 
								'BusinessSubType' => '', 										// The sub type of the business.  Values are:  ENTITY, EMANATION, ESTD_COMMONWEALTH, ESTD_UNDER_STATE_TERRITORY, ESTD_UNDER_FOREIGH_COUNTY, INCORPORATED, NON_INCORPORATED
								'BusinessType' => '', 											// Required.  The type of business.  Values:  CORPORATION, GOVERNMENT, INDIVIDUAL, NONPROFIT, PARTNERSHIP, PROPRIETORSHIP
								'Category' => '', 												// The catgory describing the business.  (ie. 1004 for Baby).  Required unless you specify MerchantCategoryCode.  PayPal uses the industry standard Merchant Category Codes.  Refer to the business' Association Merchant Category Code documentation for this list of codes.
								'CommercialRegistrationLocation' => '', 						// Official commercial registration location for the business.  Required for Germany.  Do not specify this field for other countries.
								'CompanyID' => '', 												// The ID number, equivalent to the tax ID in the US, of the business.  Required for Canada, and some accounts in Australia and Germany.
								'CustomerServiceEmail' => '', 									// Required.  The email address for the customer service dept. of the business.
								'CustomerServicePhone' => '', 									// The phone number for the customer service dept of the business.  Required for US accounts.  Otherwise optional.
								'DateOfEstablishment' => '', 									// The date of establishment for the business.  Required for most countries.
								'DisputeEmail' => '', 											// The email address to contact to dispute charges.
								'DoingBusinessAs' => '', 										// The business name being used if it is not the actual name of the business.
								'EstablishmentCountryCode' => '', 								// The country code of the country where the business was established.
								'EstablishmentState' => '', 									// The state in which the business was established.
								'IncorporationID' => '', 										// The incorporation ID number for the business.
								'MerchantCategoryCode' => '', 									// The category code for the business state in which the business was established.  Required unless you specify both Category and SubCategory.  PayPal uses the industry standard Merchant Category Codes.  Refer to the business' Association Merchant Category Code documentation for this list of codes.
								'PercentageRevenueFromOnline' => '', 							// The percentage of online sales for the business from 0 - 100.  Required for US, Canada, UK, France, Czech Republic, New Zealand, Switzerland, and Israel.  Do not specify for other countries.
								'SalesVenu' => '', 												// The venu type for sales.  Required for all countries except Czech Republic and Australia.  Values are:  WEB, EBAY, OTHER_MARKETPLACE, OTHER
								'SalesVenuDesc' => '', 											// A description of the sales venue.  Required if SalesVenu is OTHER for all countries except Czech Rep and Australia.  Do not specify for CR or Aus
								'SubCategory' => '', 											// The subcategory describing the business. PayPal uses the industry standard Merchant Category Codes.  Refer to the business' Association Merchant Category Code documentation for this list of codes. 
								'VatCountryCode' => '', 										// The country for the VAT.  Optional for business accounts in UK, France, Germany, Spain, Italy, Nertherlands, Sqitzerland, Sweden, and Denmark.  Do not specify for other countries.
								'VatID' => '', 													// The VAT ID number of the business.   Optional for business accounts in UK, France, Germany, Spain, Italy, Nertherlands, Sqitzerland, Sweden, and Denmark. Do not specify for other countries.
								'WebSite' => '', 												// The URL of the website for the business.  Required if SalesVenue is WEB.
								'WorkPhone' => '' 												// Required.  The phone number for the business.  Not required for Mexico.
							);
							
		$BusinessAddress = array(
							   'Line1' => '', 													// Required.  Street address.
							   'Line2' => '', 													// Street address 2.
							   'City' => '', 													// Required.  City
							   'State' => '', 													// State or Province
							   'PostalCode' => '', 												// Postal code
							   'CountryCode' => ''												// Required.  The country code.
							   );
							   
		$PrinciplePlaceOfBusinessAddress = array(
							   'Line1' => '', 													// Required.  Street address.
							   'Line2' => '', 													// Street address 2.
							   'City' => '', 													// Required.  City
							   'State' => '', 													// State or Province
							   'PostalCode' => '', 												// Postal code
							   'CountryCode' => ''												// Required.  The country code.
							   );
		
		$RegisteredOfficeAddress = array(
							   'Line1' => '', 													// Required.  Street address.
							   'Line2' => '', 													// Street address 2.
							   'City' => '', 													// Required.  City
							   'State' => '', 													// State or Province
							   'PostalCode' => '', 												// Postal code
							   'CountryCode' => ''												// Required.  The country code.
							   );
		
		$BusinessStakeHolder = array(
									'DateOfBirth' => '', 										// The date of birth of the stakeholder in the business.  Format:  YYYY-MM-DDZ  (ie. 1970-01-01Z)
									'FullLegalName' => '', 										// The legal name of the stakeholder in the business for which the account is being created. 
									'Saluation' => '', 											// A saluation for the account holder.
									'FirstName' => '', 											// Required.  First name of the account holder.
									'MiddleName' => '', 										// Middle name of the account holder.
									'LastName' => '', 											// Required.  Last name of the account holder.
									'Suffix' => '',  											// Suffix name for the account holder.
									'Role' => '', 												// The role of the stakeholder in the business.  Values are:  CHAIRMAN, SECRETARY, TREASURER, BENEFICIAL_OWNER, PRIMARY_CONTACT, INDIVIDUAL_PARTNER, NON_INDIVIDUAL_PARTNER, PRIMARY_INDIVIDUAL_PARTNER, DIRECTOR, NO_BENEFICIAL_OWNER
									'CountryCode' => ''											// The country code of the stakeholder's address.
									);
		
		$BusinessStakeHolderAddress = array(
							   'Line1' => '', 													// Required.  Street address.
							   'Line2' => '', 													// Street address 2.
							   'City' => '', 													// Required.  City
							   'State' => '', 													// State or Province
							   'PostalCode' => '', 												// Postal code
							   'CountryCode' => ''												// Required.  The country code.
							   );						
		
		
		$Address = array(
					   'Line1' => '', 															// Required.  Street address.
					   'Line2' => '', 															// Street address 2.
					   'City' => '', 															// Required.  City
					   'State' => '', 															// State or Province
					   'PostalCode' => '', 														// Postal code
					   'CountryCode' => ''														// Required.  The country code.
					   );
		
		$PartnerFields = array(
							   'Field1' => '', 											// Custom field for use however needed
							   'Field2' => '', 											
							   'Field3' => '', 
							   'Field4' => '', 
							   'Field5' => ''
							   );
		
		$PayPalRequestData = array(
								   'CreateAccountFields' => $CreateAccountFields, 
								   'BusinessInfo' => $BusinessInfo, 
								   'BusinessAddress' => $BusinessAddress, 
								   'PrinciplePlaceOfBusinessAddress' => $PrinciplePlaceOfBusinessAddress, 
								   'RegisteredOfficeAddress' => $RegisteredOfficeAddress, 
								   'BusinessStakeHolder' => $BusinessStakeHolder, 
								   'BusinessStakeHolderAddress' => $BusinessStakeHolderAddress, 
								   'Address' => $Address, 
								   'PartnerFields' => $PartnerFields
								   );	
								   
		$PayPalResult = $this->paypal_adaptive->CreateAccount($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	function Add_bank_account()
	{
		// Prepare request arrays
		$AddBankAccountFields = array(
									'AccountHolderDateOfBirth' => '', 									// The date of birth of the account holder.  Format:  YYYY-MM-DDZ (ie. 1970-01-01Z)
									'AccountID' => '', 													// The ID number of the PayPal account for which a bank account is added.  You must specify either AccountID or EmailAddress for this request.
									'AgencyNumber' => '', 												// For the Brazil Agency Number
									'BankAccountNumber' => '', 											// The account number (BBAN) of the bank account to be added.
									'BankAccountType' => '', 											// The type of bank account to be added.  Values are:  CHECKING, SAVINGS, BUSINESS_SAVINGS, BUSINESS_CHECKING, NORMAL, UNKNOWN
									'BankCode' => '', 													// The code that identifies the bank where the account is held.
									'BankCountryCode' => '', 											// Required.  The country code of the bank.
									'BankName' => '', 													// The name of the bank.  
									'BankTransitNumber' => '', 											// The transit number of the bank.
									'BranchCode' => '', 												// The branch code for the bank.
									'BranchLocation' => '', 											// The branch location.
									'BSBNumber' => '', 													// The Bank/State/Branch number for the bank.
									'CLABE' => '', 														// CLABE represents the bank information for countries like Mexico.
									'ConfirmationType' => '', 											// Required.  Whether PayPal account holders are redirected to PayPal.com to confirm the bank account addition.  When you pass NONE for this param, the addition is made without the account holder's explicit confirmation.  If you pass WEB, a URL is returned.  Values are:  WEB, NONE.  NONE requires advanced permissions.
									'ControlDigit' => '', 												// The control digits for the bank.
									'EmailAddress' => '', 												// The email address of the PayPal account holder.  You must specify either AccountID or EmailAddress.
									'IBAN' => '', 														// The IBAN for the bank.
									'InstitutionNumber' => '', 											// The institution number for the bank.
									'PartnerInfo' => '', 												// The partner informatoin for the bank.
									'RibKey' => '', 													// The RIB Key for the bank
									'RoutingNumber' => '', 												// The bank's routing number.
									'SortCode' => '', 													// The branch sort code.
									'TaxIDType' => '', 													// Tax ID type of CNPJ or CPF, only supported for Brazil
									'TaxIDNumber' => '' 												// Tax ID number for Brazil
									);
									
		$WebOptions = array(
							'CancelURL' => '', 															// The URL to which the user is returned when they cancel the flow at PayPal.com
							'CancelURLDescription' => '', 												// A description for the CancelURL
							'ReturnURL' => '', 															// The URL to which the user is returned when they complete the process.
							'ReturnURLDescription' => ''												// A description for the ReturnURL
							);
		
		$PayPalRequestData = array(
								   'AddBankAccountFields' => $AddBankAccountFields, 
								   'WebOptions' => $WebOptions
								   );	
								   
		$PayPalResult = $this->paypal_adaptive->AddBankAccount($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Add_payment_card()
	{
		// Prepare request arrays
		$AddPaymentCardFields = array(
										'AccountID' => '', 												// The ID number of the PayPal account for which the payment card is being added.  You must specify either AccountID or EmailAdddress
										'CardNumber' => '', 											// Required.  The credit card number.
										'CardOwnerDateOfBirth' => '', 									// The date of birth of the card holder.
										'CardType' => '', 												// Required.  The type of card being added.  Values are:  Visa, MasterCard, AmericanExpress, Discover, SwitchMaestro, Solo, CarteAurore, CarteBleue, Cofinoga, 4etoiles, CarteAura, TarjetaAurora, JCB
										'CardVerificationNumber' => '', 								// The verification code for the card.  Generally required for calls where ConfirmationType is set to NONE.  With the appropriate account review, this param is optional.
										'ConfirmationType' => '', 										// Required.  Whether the account holder is redirected to PayPal.com to confirm the card addition.  Values are:  WEB, NONE
										'CreateAccountKey' => '', 										// The key returned in a CreateAccount response.  Required for calls where the ConfirmationType is NONE.
										'EmailAddress' => '', 											// Email address of the account holder adding the card.  Must specify either AccountID or EmailAddress.
										'IssueNumber' => '', 											// The 2-digit issue number for Switch, Maestro, and Solo cards.
										'StartDate' => '' 												// The element containing the start date for the payment card.
									);
									
		$NameOnCard = array(
							'Salutation' => '', 														// A salutation for the card owner.
							'FirstName' => '', 															// Required.  First name of the card holder.
							'MiddleName' => '', 														// Middle name of the card holder.
							'LastName' => '', 															// Required.  Last name of the card holder.
							'Suffix' => ''																// A suffix for the card holder.
							);
									
		$BillingAddress = array(
								'Line1' => '', 															// Required.  Billing street address.
								'Line2' => '', 															// Billing street address 2
								'City' => '', 															// Required.  Billing city.
								'State' => '', 															// Billing state.
								'PostalCode' => '',														// Billing postal code
								'CountryCode' => ''														// Required.  Billing country code.
								);
		
		$ExpirationDate = array(
								'Month' => '', 															// Expiration month.
								'Year' => ''															// Required.  Expiration Year.
								);
									
		$WebOptions = array(
							'CancelURL' => '', 															// The URL to which the user is returned when they cancel the flow at PayPal.com
							'CancelURLDescription' => '', 												// A description for the CancelURL
							'ReturnURL' => '', 															// The URL to which the user is returned when they complete the process.
							'ReturnURLDescription' => ''												// A description for the ReturnURL
							);
		
		$PayPalRequestData = array(
								   'AddPaymentCardFields' => $AddPaymentCardFields, 
								   'NameOnCard' => $NameOnCard, 
								   'BillingAddress' => $BillingAddress, 
								   'ExpirationDate' => $ExpirationDate, 
								   'WebOptions' => $WebOptions
								   );
						   
		$PayPalResult = $this->paypal_adaptive->AddPaymentCard($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Set_funding_source_confirmed()
	{
		// Prepare request arrays
		$SetFundingSourceConfirmedFields = array(
												'AccountID' => '', 													// The ID number of the PayPal account for which a bank account is added.  You must specify either AccountID or EmailAddress for this request.
												'EmailAddress' => '', 												// The email address of the PayPal account holder.  You must specify either AccountID or EmailAddress.
												'FundingSourceKey' => ''											// The funding source key reeturned in the AddBankAccount or AddPaymentCard response.
												);
		
		$PayPalRequestData = array('SetFundingSourceConfirmedFields' => $SetFundingSourceConfirmedFields);

		$PayPalResult = $this->paypal_adaptive->SetFundingSourceConfirmed($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Get_verified_status()
	{
		// Prepare request arrays
		$GetVerifiedStatusFields = array(
										'EmailAddress' => '', 					// Required.  The email address of the PayPal account holder.
										'FirstName' => '', 						// The first name of the PayPal account holder.  Required if MatchCriteria is NAME
										'LastName' => '', 						// The last name of the PayPal account holder.  Required if MatchCriteria is NAME
										'MatchCriteria' => ''					// Required.  The criteria must be matched in addition to EmailAddress.  Currently, only NAME is supported.  Values:  NAME, NONE   To use NONE you have to be granted advanced permissions
										);
		
		$PayPalRequestData = array('GetVerifiedStatusFields' => $GetVerifiedStatusFields);

		$PayPalResult = $this->paypal_adaptive->GetVerifiedStatus($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Get_funding_plans()
	{
		// Prepare request arrays
		$GetFundingPlansFields = array(
									'PayKey' => ''		// Required.  The key used to create the payment whose funding sources you want to determine.
									);
		
		$PayPalRequestData = array('GetFundingPlansFields' => $GetFundingPlansFields);	
		
		$PayPalResult = $this->paypal_adaptive->GetFundingPlans($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_shipping_addresses()
	{
		// Prepare request arrays
		$PayKey = '';		
		$PayPalResult = $this->paypal_adaptive->GetShippingAddresses($PayKey);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Create_invoice()
	{
		// Prepare request arrays
		$CreateInvoiceFields = array(
									'MerchantEmail' => '', 				// Required.  Merchant email address.
									'PayerEmail' => '', 				// Required.  Payer email address.
									'Number' => '', 					// Unique ID for the invoice.
									'CurrencyCode' => '', 				// Required.  Currency used for all invoice item amounts and totals.
									'InvoiceDate' => '', 				// Date on which the invoice is enabled.
									'DueDate' => '', 					// Date on which the invoice payment is due.
									'PaymentTerms' => '', 				// Required.  Terms by which the invoice payment is due.  Values are:  DueOnReceipt, DueOnSpecified, Net10, Net15, Net30, Net45
									'DiscountPercent' => '', 			// Discount percent applied to the invoice.
									'DiscountAmount' => '', 			// Discount amount applied to the invoice.  If DiscountPercent is provided, DiscountAmount is ignored.
									'Terms' => '', 						// General terms for the invoice.
									'Note' => '', 						// Note to the payer company.
									'MerchantMemo' => '', 				// Memo for bookkeeping that is private to the merchant.
									'ShippingAmount' => '', 			// Cost of shipping
									'ShippingTaxName' => '', 			// Name of the applicable tax on the shipping cost.
									'ShippingTaxRate' => '', 			// Rate of the applicable tax on the shipping cost.
									'LogoURL' => ''						// Complete URL to an external image used as the logo, if any.
									);
									
		$BusinessInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BusinessInfoAddress = array(
									'Line1' => '', 						// Required. First line of address.
									'Line2' => '', 						// Second line of address.
									'City' => '', 						// Required. City of thte address.
									'State' => '', 						// State for the address.
									'PostalCode' => '', 				// Postal code of the address
									'CountryCode' => ''					// Required.  Country code of the address.
									);
		
		$BillingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BillingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		$ShippingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$ShippingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		// For invoice items you populate a nested array with multiple $InvoiceItem arrays.  Normally you'll be looping through cart items to populate the $InvoiceItem 
		// array and then push it into the $InvoiceItems array at the end of each loop for an entire collection of all items in $InvoiceItems.
		
		$InvoiceItems = array();
		
		$InvoiceItem = array(
							'Name' => '', 							// Required.  SKU or name of the item.
							'Description' => '', 					// Item description.
							'Date' => '', 							// Date on which the product or service was provided.
							'Quantity' => '', 						// Required.  Item count.  Values are:  0 to 10000
							'UnitPrice' => '', 						// Required.  Price of the item, in the currency specified by the invoice.
							'TaxName' => '', 						// Name of the applicable tax.
							'TaxRate' => ''							// Rate of the applicable tax.
							);
		array_push($InvoiceItems,$InvoiceItem);
		
		$PayPalRequestData = array(
								   'CreateInvoiceFields' => $CreateInvoiceFields, 
								   'BusinessInfo' => $BusinessInfo, 
								   'BusinessInfoAddress' => $BusinessInfoAddress, 
								   'BillingInfo' => $BillingInfo, 
								   'BillingInfoAddress' => $BillingInfoAddress, 
								   'ShippingInfo' => $ShippingInfo, 
								   'ShippingInfoAddress' => $ShippingInfoAddress, 
								   'InvoiceItems' => $InvoiceItems
								   );
						   
		$PayPalResult = $this->paypal_adaptive->CreateInvoice($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Send_invoice()
	{
		$InvoiceID = ''; // Required.  Invoice ID of the invoice to send.
		$PayPalResult = $this->paypal_adaptive->SendInvoice($InvoiceID);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Create_and_send_invoice()
	{
		// Prepare request arrays
		$CreateInvoiceFields = array(
									'MerchantEmail' => '', 				// Required.  Merchant email address.
									'PayerEmail' => '', 				// Required.  Payer email address.
									'Number' => '', 					// Unique ID for the invoice.
									'CurrencyCode' => '', 				// Required.  Currency used for all invoice item amounts and totals.
									'InvoiceDate' => '', 				// Date on which the invoice is enabled.
									'DueDate' => '', 					// Date on which the invoice payment is due.
									'PaymentTerms' => '', 				// Required.  Terms by which the invoice payment is due.  Values are:  DueOnReceipt, DueOnSpecified, Net10, Net15, Net30, Net45
									'DiscountPercent' => '', 			// Discount percent applied to the invoice.
									'DiscountAmount' => '', 			// Discount amount applied to the invoice.  If DiscountPercent is provided, DiscountAmount is ignored.
									'Terms' => '', 						// General terms for the invoice.
									'Note' => '', 						// Note to the payer company.
									'MerchantMemo' => '', 				// Memo for bookkeeping that is private to the merchant.
									'ShippingAmount' => '', 			// Cost of shipping
									'ShippingTaxName' => '', 			// Name of the applicable tax on the shipping cost.
									'ShippingTaxRate' => '', 			// Rate of the applicable tax on the shipping cost.
									'LogoURL' => ''						// Complete URL to an external image used as the logo, if any.
									);
									
		$BusinessInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BusinessInfoAddress = array(
									'Line1' => '', 						// Required. First line of address.
									'Line2' => '', 						// Second line of address.
									'City' => '', 						// Required. City of thte address.
									'State' => '', 						// State for the address.
									'PostalCode' => '', 				// Postal code of the address
									'CountryCode' => ''					// Required.  Country code of the address.
									);
		
		$BillingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BillingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		$ShippingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$ShippingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		// For invoice items you populate a nested array with multiple $InvoiceItem arrays.  Normally you'll be looping through cart items to populate the $InvoiceItem 
		// array and then push it into the $InvoiceItems array at the end of each loop for an entire collection of all items in $InvoiceItems.
		
		$InvoiceItems = array();
		
		$InvoiceItem = array(
							'Name' => '', 							// Required.  SKU or name of the item.
							'Description' => '', 					// Item description.
							'Date' => '', 							// Date on which the product or service was provided.
							'Quantity' => '', 						// Required.  Item count.  Values are:  0 to 10000
							'UnitPrice' => '', 						// Required.  Price of the item, in the currency specified by the invoice.
							'TaxName' => '', 						// Name of the applicable tax.
							'TaxRate' => ''							// Rate of the applicable tax.
							);
		array_push($InvoiceItems,$InvoiceItem);
		
		$PayPalRequestData = array(
								   'CreateInvoiceFields' => $CreateInvoiceFields, 
								   'BusinessInfo' => $BusinessInfo, 
								   'BusinessInfoAddress' => $BusinessInfoAddress, 
								   'BillingInfo' => $BillingInfo, 
								   'BillingInfoAddress' => $BillingInfoAddress, 
								   'ShippingInfo' => $ShippingInfo, 
								   'ShippingInfoAddress' => $ShippingInfoAddress, 
								   'InvoiceItems' => $InvoiceItems
								   );
						   
		$PayPalResult = $this->paypal_adaptive->CreateAndSendInvoice($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Update_invoice()
	{
		// Prepare request arrays
		$UpdateInvoiceFields = array(
									'InvoiceID' => '', 					// Required.  ID of the invoice to update.
									'MerchantEmail' => '', 				// Required.  Merchant email address.
									'PayerEmail' => '', 				// Required.  Payer email address.
									'Number' => '', 					// Unique ID for the invoice.
									'CurrencyCode' => '', 				// Required.  Currency used for all invoice item amounts and totals.
									'InvoiceDate' => '', 				// Date on which the invoice is enabled.
									'DueDate' => '', 					// Date on which the invoice payment is due.
									'PaymentTerms' => '', 				// Required.  Terms by which the invoice payment is due.  Values are:  DueOnReceipt, DueOnSpecified, Net10, Net15, Net30, Net45
									'DiscountPercent' => '', 			// Discount percent applied to the invoice.
									'DiscountAmount' => '', 			// Discount amount applied to the invoice.  If DiscountPercent is provided, DiscountAmount is ignored.
									'Terms' => '', 						// General terms for the invoice.
									'Note' => '', 						// Note to the payer company.
									'MerchantMemo' => '', 				// Memo for bookkeeping that is private to the merchant.
									'ShippingAmount' => '', 			// Cost of shipping
									'ShippingTaxName' => '', 			// Name of the applicable tax on the shipping cost.
									'ShippingTaxRate' => '', 			// Rate of the applicable tax on the shipping cost.
									'LogoURL' => ''						// Complete URL to an external image used as the logo, if any.
									);
									
		$BusinessInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BusinessInfoAddress = array(
									'Line1' => '', 						// Required. First line of address.
									'Line2' => '', 						// Second line of address.
									'City' => '', 						// Required. City of thte address.
									'State' => '', 						// State for the address.
									'PostalCode' => '', 				// Postal code of the address
									'CountryCode' => ''					// Required.  Country code of the address.
									);
		
		$BillingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$BillingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		$ShippingInfo = array(
							'FirstName' => '', 							// First name of the company contact.
							'LastName' => '', 							// Last name of the company contact.
							'BusinessName' => '', 						// Company business name.
							'Phone' => '', 								// Phone number for contacting the company.
							'Fax' => '', 								// Fax number used by the company.
							'Website' => '', 							// Website used by the company.
							'Custom' => '' 								// Custom value to be displayed in the contact information details.
							);
							
		$ShippingInfoAddress = array(
								'Line1' => '', 						// Required. First line of address.
								'Line2' => '', 						// Second line of address.
								'City' => '', 						// Required. City of thte address.
								'State' => '', 						// State for the address.
								'PostalCode' => '', 				// Postal code of the address
								'CountryCode' => ''					// Required.  Country code of the address.
								);
		
		// For invoice items you populate a nested array with multiple $InvoiceItem arrays.  Normally you'll be looping through cart items to populate the $InvoiceItem 
		// array and then push it into the $InvoiceItems array at the end of each loop for an entire collection of all items in $InvoiceItems.
		
		$InvoiceItems = array();
		
		$InvoiceItem = array(
							'Name' => '', 							// Required.  SKU or name of the item.
							'Description' => '', 					// Item description.
							'Date' => '', 							// Date on which the product or service was provided.
							'Quantity' => '', 						// Required.  Item count.  Values are:  0 to 10000
							'UnitPrice' => '', 						// Required.  Price of the item, in the currency specified by the invoice.
							'TaxName' => '', 						// Name of the applicable tax.
							'TaxRate' => ''							// Rate of the applicable tax.
							);
		array_push($InvoiceItems,$InvoiceItem);
		
		$PayPalRequestData = array(
								   'UpdateInvoiceFields' => $UpdateInvoiceFields, 
								   'BusinessInfo' => $BusinessInfo, 
								   'BusinessInfoAddress' => $BusinessInfoAddress, 
								   'BillingInfo' => $BillingInfo, 
								   'BillingInfoAddress' => $BillingInfoAddress, 
								   'ShippingInfo' => $ShippingInfo, 
								   'ShippingInfoAddress' => $ShippingInfoAddress, 
								   'InvoiceItems' => $InvoiceItems
								   );	
								   
		$PayPalResult = $this->paypal_adaptive->UpdateInvoice($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_invoice_details()
	{
		$InvoiceID = '';
		$PayPalResult = $this->paypal_adaptive->GetInvoiceDetails($InvoiceID);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Cancel_invoice()
	{
		// Prepare request arrays
		$CancelInvoiceFields = array(
									'InvoiceID' => '', 			// ID of the invoice.
									'Subject' => '', 			// Subject of the cancelation notification.
									'NoteForPayer' => '', 		// Note to send the payer within the cancelation notification.
									'SendCopyToMerchant' => ''	// Indicates whether to send a copy of the cancelation notification to the merchant.  Values are:  true/false
									);
		
		$PayPalRequestData = array('CancelInvoiceFields' => $CancelInvoiceFields);

		$PayPalResult = $this->paypal_adaptive->CancelInvoice($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Search_invoices()
	{
		// Prepare request arrays
		$SearchInvoicesFields = array(
									'MerchantEmail' => '', 			// Required.  Email address of invoice creator.
									'Page' => '', 					// Required.  Page number of result set, starting with 1
									'PageSize' => ''				// Required.  Number of result pages, between 1 and 100
									);
		
		$Parameters = array(
							'Email' => '', 															// Email search string
							'RecipientName' => '', 													// Recipient search string
							'BusinessName' => '', 													// Company search string
							'InvoiceNumber' => '', 													// Invoice number search string
							'Status' => '', 														// Invoice status search
							'LowerAmount' => '', 													// Invoice amount search.  It specifies the smallest amount to be returned.  If you pass a value for this field, you must also pass a CurrencyCode value.
							'UpperAmount' => '', 													// Invoice amount search.  It specifies the largest amount to be returned.  If you pass a value for this field, you must also pass a CurrencyCode value.
							'CurrencyCode' => '', 													// Currency used for lower and upper amounts.  
							'Memo' => '', 															// Invoice memo search string
							'Origin' => '', 														// Indicates whether the invoice was created by the website or by an API call.  Values are:  Web, API
							'InvoiceDate' => array('StartDate' => '', 'EndDate' => ''), 			// Invoice date range filter
							'DueDate' => array('StartDate' => '', 'EndDate' => ''), 				// Invoice due Date range filter
							'PaymentDate' => array('StartDate' => '', 'EndDate' => ''), 			// Invoice payment date range filter.
							'CreationDate' => array('StartDate' => '', 'EndDate' => '')				// Invoice creation date range filter.
							);
		
		$PayPalRequestData = array(
								   'SearchInvoicesFields' => $SearchInvoicesFields, 
								   'Parameters' => $Parameters
								   );
						   
		$PayPalResult = $this->paypal_adaptive->SearchInvoices($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Mark_invoice_as_paid()
	{
		// Prepare request arrays
		$MarkInvoiceAsPaidFields = array(
										'InvoiceID' => '', 			// Required.  ID of the invoice to mark paid.
										'Method' => '', 			// Method t hat can be used to mark an invoice as paid when the payer p ays offline.  Values are:  BankTransfer, Cash, Check, CreditCard, DebitCard, Other, PayPal, WireTransfer
										'Note' => '', 				// Optional note associated with the payment.
										'Date' => ''				// Date the invoice was paid.
									);
		
		$PayPalRequestData = array('MarkInvoiceAsPaidFields' => $MarkInvoiceAsPaidFields);

		$PayPalResult = $this->paypal_adaptive->MarkInvoiceAsPaid($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Request_permissions()
	{
		// Prepare request arrays
		$Scope = array(
						'EXPRESS_CHECKOUT', 
						'DIRECT_PAYMENT', 
						'SETTLEMENT_CONSOLIDATION', 
						'SETTLEMENT_REPORTING', 
						'AUTH_CAPTURE', 
						'MOBILE_CHECKOUT', 
						'BILLING_AGREEMENT', 
						'REFERENCE_TRANSACTION', 
						'AIR_TRAVEL', 
						'MASS_PAY', 
						'TRANSACTION_DETAILS',
						'TRANSACTION_SEARCH',
						'RECURRING_PAYMENTS',
						'ACCOUNT_BALANCE',
						'ENCRYPTED_WEBSITE_PAYMENTS',
						'REFUND',
						'NON_REFERENCED_CREDIT',
						'BUTTON_MANAGER',
						'MANAGE_PENDING_TRANSACTION_STATUS',
						'RECURRING_PAYMENT_REPORT',
						'EXTENDED_PRO_PROCESSING_REPORT',
						'EXCEPTION_PROCESSING_REPORT',
						'ACCOUNT_MANAGEMENT_PERMISSIONS',
						'ACCESS_BASIC_PERSONAL_DATA',
						'ACCESS_ADVANCED_PERSONAL_DATA'
						);
		
		$RequestPermissionsFields = array(
										'Scope' => $Scope, 				// Required.   
										'Callback' => ''			// Required.  Your callback function that specifies actions to take after the account holder grants or denies the request.
										);
										
		$PayPalRequestData = array('RequestPermissionsFields'=>$RequestPermissionsFields);
								
		$PayPalResult = $this->paypal_adaptive->RequestPermissions($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_access_token()
	{
		// Prepare request arrays
		$GetAccessTokenFields = array(
									'Token' => '', 					// Required.  The request token from the response to RequestPermissions
									'Verifier' => '' 				// Required.  The verification code returned in the redirect from PayPal to the return URL.
									);
		
		$PayPalRequestData = array('GetAccessTokenFields' => $GetAccessTokenFields);

		$PayPalResult = $this->paypal_adaptive->GetAccessToken($PayPalRequestData);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_permissions()
	{
		$Token = '';
		$PayPalResult = $this->paypal_adaptive->GetPermissions($Token);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Cancel_permissions()
	{
		$Token = '';
		$PayPalResult = $this->paypal_adaptive->CancelPermissions($Token);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Get_basic_personal_data()
	{
		// Prepare request arrays
		$AttributeList = array(
								'http://axschema.org/namePerson/first',
								'http://axschema.org/namePerson/last',
								'http://axschema.org/contact/email',
								'http://axschema.org/contact/fullname',
								'http://openid.net/schema/company/name',
								'http://axschema.org/contact/country/home',
								'https://www.paypal.com/webapps/auth/schema/payerID'
							);
							
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $this->paypal_adaptive->GetBasicPersonalData($AttributeList);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_advanced_personal_data()
	{
		// Prepare request arrays
		$AttributeList = array(
						'http://axschema.org/birthDate',
						'http://axschema.org/contact/postalCode/home',
						'http://schema.openid.net/contact/street1',
						'http://schema.openid.net/contact/street2',
						'http://axschema.org/contact/city/home',
						'http://axschema.org/contact/state/home',
						'http://axschema.org/contact/phone/default'
					);
							
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $this->paypal_adaptive->GetAdvancedPersonalData($AttributeList);
		
		if(!$this->paypal_adaptive->APICallSuccessful($PayPalResult['Ack']))
		{
			$errors = array('Errors'=>$PayPalResult['Errors']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
}

/* End of file adaptive_payments.php */
/* Location: ./system/application/controllers/paypal/adaptive_payments.php */