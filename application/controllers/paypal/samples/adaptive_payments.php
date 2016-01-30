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
		$this->load->view('paypal/samples/adaptive_payments');
	}

	
	function Pay_chained()
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
			$this->load->view('paypal/samples/error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			echo '<pre />';
			print_r($PayPalResult);
			echo '<p><a href="' . $PayPalResult['RedirectURL'] . '">PROCEED TO PAYPAL</a></p>';
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
			$this->load->view('paypal/samples/error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('paypal/samples/convert_currency',$data);
		}	
	}

}
/* End of file Adaptive_payments.php */
/* Location: ./system/application/controllers/paypal/samples/Adaptive_payments.php */