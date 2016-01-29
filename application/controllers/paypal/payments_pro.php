<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payments_pro extends CI_Controller 
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
			'APIVersion' => $this->config->item('APIVersion')		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
		);
		
		// Show Errors
		if($config['Sandbox'])
		{
			error_reporting(E_ALL);
			ini_set('display_errors', '1');
		}
		
		$this->load->library('paypal/Paypal_pro', $config);	
	}
	
	
	function index()
	{
		$this->load->view('payments_pro_demo');
	}
	
	
	function Do_capture()
	{
		$DCFields = array(
						'authorizationid' => '', 				// Required. The authorization identification number of the payment you want to capture. This is the transaction ID returned from DoExpressCheckoutPayment or DoDirectPayment.
						'amt' => '', 							// Required. Must have two decimal places.  Decimal separator must be a period (.) and optional thousands separator must be a comma (,)
						'completetype' => '', 					// Required.  The value Complete indiciates that this is the last capture you intend to make.  The value NotComplete indicates that you intend to make additional captures.
						'currencycode' => '', 					// Three-character currency code
						'invnum' => '', 						// Your invoice number
						'note' => '', 							// Informational note about this setlement that is displayed to the buyer in an email and in his transaction history.  255 character max.
						'softdescriptor' => '', 				// Per transaction description of the payment that is passed to the customer's credit card statement.
						'storeid' => '', 						// ID of the merchant store.  This field is required for point-of-sale transactions.  Max: 50 char
						'terminalid' => ''						// ID of the terminal.  50 char max.  
					);
					
		$PayPalRequestData = array('DCFields' => $DCFields);
		$PayPalResult = $this->paypal_pro->DoCapture($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_authorization()
	{
		$DAFields = array(
							'transactionid' => '', 					// Required.  The value of the order's transaction ID number returned by PayPal.  
							'amt' => '', 							// Required. Must have two decimal places.  Decimal separator must be a period (.) and optional thousands separator must be a comma (,)
							'transactionentity' => '', 				// Type of transaction to authorize.  The only allowable value is Order, which means that the transaction represents a customer order that can be fulfilled over 29 days.
							'currencycode' => '', 					// Three-character currency code.
							'msgsubid' => ''						// A message ID used for idempotence to uniquely identify a message.
						);
						
		$PayPalRequestData = array('DAFields' => $DAFields);
		$PayPalResult = $this->paypal_pro->DoAuthorization($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Do_reauthorization()
	{
		$DRFields = array(
						'authorizationid' => '', 				// Required. The value of a previously authorized transaction ID returned by PayPal.
						'amt' => '', 							// Required. Must have two decimal places.  Decimal separator must be a period (.) and optional thousands separator must be a comma (,)
						'currencycode' => '',					// Three-character currency code.
						'msgsubid' => ''						// A message ID used for idempotence to uniquely identify a message.
					);	
					
		$PayPalRequestData = array('DRFields' => $DRFields);
		$PayPalResult = $this->paypal_pro->DoReauthorization($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_void()
	{
		$DVFields = array(
						'authorizationid' => '', 				// Required.  The value of the original authorization ID returned by PayPal.  NOTE:  If voiding a transaction that has been reauthorized, use the ID from the original authorization, not the reauth.
						'note' => '',  							// An information note about this void that is displayed to the payer in an email and in his transaction history.  255 char max.
						'msgsubid' => ''						// A message ID used for idempotence to uniquely identify a message.
					);	
					
		$PayPalRequestData = array('DVFields' => $DVFields);
		$PayPalResult = $this->paypal_pro->DoVoid($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Mass_pay()
	{
		$MPFields = array(
							'emailsubject' => '', 						// The subject line of the email that PayPal sends when the transaction is completed.  Same for all recipients.  255 char max.
							'currencycode' => '', 						// Three-letter currency code.
							'receivertype' => '' 						// Indicates how you identify the recipients of payments in this call to MassPay.  Must be EmailAddress or UserID
						);
		
		// MassPay accepts multiple payments in a single call.  
		// Therefore, we must create an array of payments to pass into the class.
		// In this sample we're simply passing in 2 separate payments with static amounts.
		// In most cases you'll be looping through records in a data source to generate the $MPItems array below.
		
		$Item1 = array(
					'l_email' => '', 							// Required.  Email address of recipient.  You must specify either L_EMAIL or L_RECEIVERID but you must not mix the two.
					'l_receiverid' => '', 						// Required.  ReceiverID of recipient.  Must specify this or email address, but not both.
					'l_amt' => '', 								// Required.  Payment amount.
					'l_uniqueid' => '', 						// Transaction-specific ID number for tracking in an accounting system.
					'l_note' => '' 								// Custom note for each recipient.
				);
				
		$Item2 = array(
					'l_email' => '', 							// Required.  Email address of recipient.  You must specify either L_EMAIL or L_RECEIVERID but you must not mix the two.
					'l_receiverid' => '', 						// Required.  ReceiverID of recipient.  Must specify this or email address, but not both.
					'l_amt' => '', 								// Required.  Payment amount.
					'l_uniqueid' => '', 						// Transaction-specific ID number for tracking in an accounting system.
					'l_note' => '' 								// Custom note for each recipient.
				);
									
		$MPItems = array($Item1, $Item2);
		
		$PayPalRequestData = array(
						'MPFields' => $MPFields, 
						'MPItems' => $MPItems
					);
					
		$PayPalResult = $this->paypal_pro->MassPay($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	
	}
	
	
	function Refund_transaction()
	{
		$RTFields = array(
					'transactionid' => '', 							// Required.  PayPal transaction ID for the order you're refunding.
					'payerid' => '', 								// Encrypted PayPal customer account ID number.  Note:  Either transaction ID or payer ID must be specified.  127 char max
					'invoiceid' => '', 								// Your own invoice tracking number.
					'refundtype' => '', 							// Required.  Type of refund.  Must be Full, Partial, or Other.
					'amt' => '', 									// Refund Amt.  Required if refund type is Partial.  
					'currencycode' => '', 							// Three-letter currency code.  Required for Partial Refunds.  Do not use for full refunds.
					'note' => '',  									// Custom memo about the refund.  255 char max.
					'retryuntil' => '', 							// Maximum time until you must retry the refund.  Note:  this field does not apply to point-of-sale transactions.
					'refundsource' => '', 							// Type of PayPal funding source (balance or eCheck) that can be used for auto refund.  Values are:  any, default, instant, eCheck
					'merchantstoredetail' => '', 					// Information about the merchant store.
					'refundadvice' => '', 							// Flag to indicate that the buyer was already given store credit for a given transaction.  Values are:  1/0
					'refunditemdetails' => '', 						// Details about the individual items to be returned.
					'msgsubid' => '', 								// A message ID used for idempotence to uniquely identify a message.
					'storeid' => '', 								// ID of a merchant store.  This field is required for point-of-sale transactions.  50 char max.
					'terminalid' => ''								// ID of the terminal.  50 char max.
				);	
					
		$PayPalRequestData = array('RTFields' => $RTFields);
		
		$PayPalResult = $this->paypal_pro->RefundTransaction($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_transaction_details()
	{
		$GTDFields = array(
							'transactionid' => ''		// PayPal transaction ID of the order you want to get details for.
						);
						
		$PayPalRequestData = array('GTDFields' => $GTDFields);
		
		$PayPalResult = $this->paypal_pro->GetTransactionDetails($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	function Do_direct_payment()
	{
		$DPFields = array(
							'paymentaction' => '', 						// How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
							'ipaddress' => '', 							// Required.  IP address of the payer's browser.
							'returnfmfdetails' => '' 					// Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
						);
						
		$CCDetails = array(
							'creditcardtype' => '', 					// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
							'acct' => '', 								// Required.  Credit card number.  No spaces or punctuation.  
							'expdate' => '', 							// Required.  Credit card expiration date.  Format is MMYYYY
							'cvv2' => '', 								// Requirements determined by your PayPal account settings.  Security digits for credit card.
							'startdate' => '', 							// Month and year that Maestro or Solo card was issued.  MMYYYY
							'issuenumber' => ''							// Issue number of Maestro or Solo card.  Two numeric digits max.
						);
						
		$PayerInfo = array(
							'email' => '', 								// Email address of payer.
							'payerid' => '', 							// Unique PayPal customer ID for payer.
							'payerstatus' => '', 						// Status of payer.  Values are verified or unverified
							'business' => '' 							// Payer's business name.
						);
						
		$PayerName = array(
							'salutation' => '', 						// Payer's salutation.  20 char max.
							'firstname' => '', 							// Payer's first name.  25 char max.
							'middlename' => '', 						// Payer's middle name.  25 char max.
							'lastname' => '', 							// Payer's last name.  25 char max.
							'suffix' => ''								// Payer's suffix.  12 char max.
						);
						
		$BillingAddress = array(
								'street' => '', 						// Required.  First street address.
								'street2' => '', 						// Second street address.
								'city' => '', 							// Required.  Name of City.
								'state' => '', 							// Required. Name of State or Province.
								'countrycode' => '', 					// Required.  Country code.
								'zip' => '', 							// Required.  Postal code of payer.
								'phonenum' => '' 						// Phone Number of payer.  20 char max.
							);
							
		$ShippingAddress = array(
								'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => '', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
								'shiptophonenum' => ''					// Phone number for shipping address.  20 char max.
								);
							
		$PaymentDetails = array(
								'amt' => '', 							// Required.  Total amount of order, including shipping, handling, and tax.  
								'currencycode' => '', 					// Required.  Three-letter currency code.  Default is USD.
								'itemamt' => '', 						// Required if you include itemized cart details. (L_AMTn, etc.)  Subtotal of items not including S&H, or tax.
								'shippingamt' => '', 					// Total shipping costs for the order.  If you specify shippingamt, you must also specify itemamt.
								'insuranceamt' => '', 					// Total shipping insurance costs for this order.  
								'shipdiscamt' => '', 					// Shipping discount for the order, specified as a negative number.
								'handlingamt' => '', 					// Total handling costs for the order.  If you specify handlingamt, you must also specify itemamt.
								'taxamt' => '', 						// Required if you specify itemized cart tax details. Sum of tax for all items on the order.  Total sales tax. 
								'desc' => '', 							// Description of the order the customer is purchasing.  127 char max.
								'custom' => '', 						// Free-form field for your own use.  256 char max.
								'invnum' => '', 						// Your own invoice or tracking number
								'buttonsource' => '', 					// An ID code for use by 3rd party apps to identify transactions.
								'notifyurl' => '', 						// URL for receiving Instant Payment Notifications.  This overrides what your profile is set to use.
								'recurring' => ''						// Flag to indicate a recurring transaction.  Value should be Y for recurring, or anything other than Y if it's not recurring.  To pass Y here, you must have an established billing agreement with the buyer.
							);
		
		// For order items you populate a nested array with multiple $Item arrays.  
		// Normally you'll be looping through cart items to populate the $Item array
		// Then push it into the $OrderItems array at the end of each loop for an entire 
		// collection of all items in $OrderItems.
				
		$OrderItems = array();
			
		$Item	 = array(
							'l_name' => '', 						// Item Name.  127 char max.
							'l_desc' => '', 						// Item description.  127 char max.
							'l_amt' => '', 							// Cost of individual item.
							'l_number' => '', 						// Item Number.  127 char max.
							'l_qty' => '', 							// Item quantity.  Must be any positive integer.  
							'l_taxamt' => '', 						// Item's sales tax amount.
							'l_ebayitemnumber' => '', 				// eBay auction number of item.
							'l_ebayitemauctiontxnid' => '', 		// eBay transaction ID of purchased item.
							'l_ebayitemorderid' => '' 				// eBay order ID for the item.
					);
		
		array_push($OrderItems, $Item);
		
		$Secure3D = array(
						  'authstatus3d' => '', 
						  'mpivendor3ds' => '', 
						  'cavv' => '', 
						  'eci3ds' => '', 
						  'xid' => ''
						  );
						  
		$PayPalRequestData = array(
								'DPFields' => $DPFields, 
								'CCDetails' => $CCDetails, 
								'PayerInfo' => $PayerInfo, 
								'PayerName' => $PayerName, 
								'BillingAddress' => $BillingAddress, 
								'ShippingAddress' => $ShippingAddress, 
								'PaymentDetails' => $PaymentDetails, 
								'OrderItems' => $OrderItems, 
								'Secure3D' => $Secure3D
							);
							
		$PayPalResult = $this->paypal_pro->DoDirectPayment($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_direct_payment_demo()
	{
		$DPFields = array(
							'paymentaction' => 'Sale', 						// How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
							'ipaddress' => $_SERVER['REMOTE_ADDR'], 							// Required.  IP address of the payer's browser.
							'returnfmfdetails' => '1' 					// Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
						);
						
		$CCDetails = array(
							'creditcardtype' => 'MasterCard', 					// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
							'acct' => '5424180818927383', 								// Required.  Credit card number.  No spaces or punctuation.  
							'expdate' => '102017', 							// Required.  Credit card expiration date.  Format is MMYYYY
							'cvv2' => '123', 								// Requirements determined by your PayPal account settings.  Security digits for credit card.
							'startdate' => '', 							// Month and year that Maestro or Solo card was issued.  MMYYYY
							'issuenumber' => ''							// Issue number of Maestro or Solo card.  Two numeric digits max.
						);
						
		$PayerInfo = array(
							'email' => 'test@domain.com', 								// Email address of payer.
							'payerid' => '', 							// Unique PayPal customer ID for payer.
							'payerstatus' => '', 						// Status of payer.  Values are verified or unverified
							'business' => 'Testers, LLC' 							// Payer's business name.
						);
						
		$PayerName = array(
							'salutation' => 'Mr.', 						// Payer's salutation.  20 char max.
							'firstname' => 'Tester', 							// Payer's first name.  25 char max.
							'middlename' => '', 						// Payer's middle name.  25 char max.
							'lastname' => 'Testerson', 							// Payer's last name.  25 char max.
							'suffix' => ''								// Payer's suffix.  12 char max.
						);
						
		$BillingAddress = array(
								'street' => '123 Test Ave.', 						// Required.  First street address.
								'street2' => '', 						// Second street address.
								'city' => 'Kansas City', 							// Required.  Name of City.
								'state' => 'MO', 							// Required. Name of State or Province.
								'countrycode' => 'US', 					// Required.  Country code.
								'zip' => '64111', 							// Required.  Postal code of payer.
								'phonenum' => '555-555-5555' 						// Phone Number of payer.  20 char max.
							);
							
		$ShippingAddress = array(
								'shiptoname' => 'Tester Testerson', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '123 Test Ave.', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => 'Kansas City', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => 'MO', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '64111', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => 'US', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
								'shiptophonenum' => '555-555-5555'					// Phone number for shipping address.  20 char max.
								);
							
		$PaymentDetails = array(
								'amt' => '100.00', 							// Required.  Total amount of order, including shipping, handling, and tax.  
								'currencycode' => 'USD', 					// Required.  Three-letter currency code.  Default is USD.
								'itemamt' => '95.00', 						// Required if you include itemized cart details. (L_AMTn, etc.)  Subtotal of items not including S&H, or tax.
								'shippingamt' => '5.00', 					// Total shipping costs for the order.  If you specify shippingamt, you must also specify itemamt.
								'shipdiscamt' => '', 					// Shipping discount for the order, specified as a negative number.  
								'handlingamt' => '', 					// Total handling costs for the order.  If you specify handlingamt, you must also specify itemamt.
								'taxamt' => '', 						// Required if you specify itemized cart tax details. Sum of tax for all items on the order.  Total sales tax. 
								'desc' => 'Web Order', 							// Description of the order the customer is purchasing.  127 char max.
								'custom' => '', 						// Free-form field for your own use.  256 char max.
								'invnum' => '', 						// Your own invoice or tracking number
								'notifyurl' => ''						// URL for receiving Instant Payment Notifications.  This overrides what your profile is set to use.
							);	
				
		$OrderItems = array();
		$Item	 = array(
							'l_name' => 'Test Widget 123', 						// Item Name.  127 char max.
							'l_desc' => 'The best test widget on the planet!', 						// Item description.  127 char max.
							'l_amt' => '95.00', 							// Cost of individual item.
							'l_number' => '123', 						// Item Number.  127 char max.
							'l_qty' => '1', 							// Item quantity.  Must be any positive integer.  
							'l_taxamt' => '', 						// Item's sales tax amount.
							'l_ebayitemnumber' => '', 				// eBay auction number of item.
							'l_ebayitemauctiontxnid' => '', 		// eBay transaction ID of purchased item.
							'l_ebayitemorderid' => '' 				// eBay order ID for the item.
					);
		array_push($OrderItems, $Item);
		
		$Secure3D = array(
						  'authstatus3d' => '', 
						  'mpivendor3ds' => '', 
						  'cavv' => '', 
						  'eci3ds' => '', 
						  'xid' => ''
						  );
						  
		$PayPalRequestData = array(
								'DPFields' => $DPFields, 
								'CCDetails' => $CCDetails, 
								'PayerInfo' => $PayerInfo, 
								'PayerName' => $PayerName, 
								'BillingAddress' => $BillingAddress, 
								'ShippingAddress' => $ShippingAddress, 
								'PaymentDetails' => $PaymentDetails, 
								'OrderItems' => $OrderItems, 
								'Secure3D' => $Secure3D
							);
							
		$PayPalResult = $this->paypal_pro->DoDirectPayment($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('do_direct_payment_demo',$data);
		}
	}
	
	
	function Set_express_checkout()
	{
		$SECFields = array(
							'token' => '', 								// A timestamped token, the value of which was returned by a previous SetExpressCheckout call.
							'maxamt' => '', 						// The expected maximum total amount the order will be, including S&H and sales tax.
							'returnurl' => '', 							// Required.  URL to which the customer will be returned after returning from PayPal.  2048 char max.
							'cancelurl' => '', 							// Required.  URL to which the customer will be returned if they cancel payment on PayPal's site.
							'callback' => '', 							// URL to which the callback request from PayPal is sent.  Must start with https:// for production.
							'callbacktimeout' => '', 					// An override for you to request more or less time to be able to process the callback request and response.  Acceptable range for override is 1-6 seconds.  If you specify greater than 6 PayPal will use default value of 3 seconds.
							'callbackversion' => '', 					// The version of the Instant Update API you're using.  The default is the current version.							
							'reqconfirmshipping' => '', 				// The value 1 indicates that you require that the customer's shipping address is Confirmed with PayPal.  This overrides anything in the account profile.  Possible values are 1 or 0.
							'noshipping' => '', 						// The value 1 indiciates that on the PayPal pages, no shipping address fields should be displayed.  Maybe 1 or 0.
							'allownote' => '', 							// The value 1 indiciates that the customer may enter a note to the merchant on the PayPal page during checkout.  The note is returned in the GetExpresscheckoutDetails response and the DoExpressCheckoutPayment response.  Must be 1 or 0.
							'addroverride' => '', 						// The value 1 indiciates that the PayPal pages should display the shipping address set by you in the SetExpressCheckout request, not the shipping address on file with PayPal.  This does not allow the customer to edit the address here.  Must be 1 or 0.
							'localecode' => '', 						// Locale of pages displayed by PayPal during checkout.  Should be a 2 character country code.  You can retrive the country code by passing the country name into the class' GetCountryCode() function.
							'pagestyle' => '', 							// Sets the Custom Payment Page Style for payment pages associated with this button/link.  
							'hdrimg' => '', 							// URL for the image displayed as the header during checkout.  Max size of 750x90.  Should be stored on an https:// server or you'll get a warning message in the browser.
							'hdrbordercolor' => '', 					// Sets the border color around the header of the payment page.  The border is a 2-pixel permiter around the header space.  Default is black.  
							'hdrbackcolor' => '', 						// Sets the background color for the header of the payment page.  Default is white.  
							'payflowcolor' => '', 						// Sets the background color for the payment page.  Default is white.
							'skipdetails' => '', 						// This is a custom field not included in the PayPal documentation.  It's used to specify whether you want to skip the GetExpressCheckoutDetails part of checkout or not.  See PayPal docs for more info.
							'email' => '', 								// Email address of the buyer as entered during checkout.  PayPal uses this value to pre-fill the PayPal sign-in page.  127 char max.
							'solutiontype' => '', 						// Type of checkout flow.  Must be Sole (express checkout for auctions) or Mark (normal express checkout)
							'landingpage' => '', 						// Type of PayPal page to display.  Can be Billing or Login.  If billing it shows a full credit card form.  If Login it just shows the login screen.
							'channeltype' => '', 						// Type of channel.  Must be Merchant (non-auction seller) or eBayItem (eBay auction)
							'giropaysuccessurl' => '', 					// The URL on the merchant site to redirect to after a successful giropay payment.  Only use this field if you are using giropay or bank transfer payment methods in Germany.
							'giropaycancelurl' => '', 					// The URL on the merchant site to redirect to after a canceled giropay payment.  Only use this field if you are using giropay or bank transfer methods in Germany.
							'banktxnpendingurl' => '',  				// The URL on the merchant site to transfer to after a bank transfter payment.  Use this field only if you are using giropay or bank transfer methods in Germany.
							'brandname' => '', 							// A label that overrides the business name in the PayPal account on the PayPal hosted checkout pages.  127 char max.
							'customerservicenumber' => '', 				// Merchant Customer Service number displayed on the PayPal Review page. 16 char max.
							'giftmessageenable' => '', 					// Enable gift message widget on the PayPal Review page. Allowable values are 0 and 1
							'giftreceiptenable' => '', 					// Enable gift receipt widget on the PayPal Review page. Allowable values are 0 and 1
							'giftwrapenable' => '', 					// Enable gift wrap widget on the PayPal Review page.  Allowable values are 0 and 1.
							'giftwrapname' => '', 						// Label for the gift wrap option such as "Box with ribbon".  25 char max.
							'giftwrapamount' => '', 					// Amount charged for gift-wrap service.
							'buyeremailoptionenable' => '', 			// Enable buyer email opt-in on the PayPal Review page. Allowable values are 0 and 1
							'surveyquestion' => '', 					// Text for the survey question on the PayPal Review page. If the survey question is present, at least 2 survey answer options need to be present.  50 char max.
							'surveyenable' => '', 						// Enable survey functionality. Allowable values are 0 and 1
							'totaltype' => '', 							// Enables display of "estimated total" instead of "total" in the cart review area.  Values are:  Total, EstimatedTotal
							'notetobuyer' => '', 						// Displays a note to buyers in the cart review area below the total amount.  Use the note to tell buyers about items in the cart, such as your return policy or that the total excludes shipping and handling.  127 char max.							
							'buyerid' => '', 							// The unique identifier provided by eBay for this buyer. The value may or may not be the same as the username. In the case of eBay, it is different. 255 char max.
							'buyerusername' => '', 						// The user name of the user at the marketplaces site.
							'buyerregistrationdate' => '',  			// Date when the user registered with the marketplace.
							'allowpushfunding' => ''					// Whether the merchant can accept push funding.  0 = Merchant can accept push funding : 1 = Merchant cannot accept push funding.			
						);
		
		// Basic array of survey choices.  Nothing but the values should go in here.  
		$SurveyChoices = array('Choice 1', 'Choice2', 'Choice3', 'etc');
		
		// You can now utlize parallel payments (split payments) within Express Checkout.
		// Here we'll gather all the payment data for each payment included in this checkout 
		// and pass them into a $Payments array.  
		
		// Keep in mind that each payment will ahve its own set of OrderItems
		// so don't get confused along the way.
		$Payments = array();
		$Payment = array(
						'amt' => '', 							// Required.  The total cost of the transaction to the customer.  If shipping cost and tax charges are known, include them in this value.  If not, this value should be the current sub-total of the order.
						'currencycode' => '', 					// A three-character currency code.  Default is USD.
						'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
						'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
						'shipdiscamt' => '', 				// Shipping discount for this order, specified as a negative number.
						'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays the string 'Yes' and the insurance amount.  If true, the total shipping insurance for this order must be a positive number.
						'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
						'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
						'desc' => '', 							// Description of items on the order.  127 char max.
						'custom' => '', 						// Free-form field for your own use.  256 char max.
						'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
						'notifyurl' => '', 						// URL for receiving Instant Payment Notifications
						'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
						'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
						'shiptostreet2' => '', 					// Second street address.  100 char max.
						'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
						'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
						'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
						'shiptocountrycode' => '', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
						'shiptophonenum' => '',  				// Phone number for shipping address.  20 char max.
						'notetext' => '', 						// Note to the merchant.  255 char max.  
						'allowedpaymentmethod' => '', 			// The payment method type.  Specify the value InstantPaymentOnly.
						'allowpushfunding' => '', 				// Whether the merchant can accept push funding:  0 - Merchant can accept push funding.  1 - Merchant cannot accept push funding.  This will override the setting in the merchant's PayPal account.
						'paymentaction' => '', 					// How you want to obtain the payment.  When implementing parallel payments, this field is required and must be set to Order. 
						'paymentrequestid' => '',  				// A unique identifier of the specific payment request, which is required for parallel payments. 
						'sellerid' => '', 						// The unique non-changing identifier for the seller at the marketplace site.  This ID is not displayed.
						'sellerusername' => '', 				// The current name of the seller or business at the marketplace site.  This name may be shown to the buyer.
						'sellerpaypalaccountid' => ''			// A unique identifier for the merchant.  For parallel payments, this field is required and must contain the Payer ID or the email address of the merchant.
						);
		
		// For order items you populate a nested array with multiple $Item arrays.  
		// Normally you'll be looping through cart items to populate the $Item array
		// Then push it into the $OrderItems array at the end of each loop for an entire 
		// collection of all items in $OrderItems.
				
		$PaymentOrderItems = array();
		$Item = array(
					'name' => '', 								// Item name. 127 char max.
					'desc' => '', 								// Item description. 127 char max.
					'amt' => '', 								// Cost of item.
					'number' => '', 							// Item number.  127 char max.
					'qty' => '', 								// Item qty on order.  Any positive integer.
					'taxamt' => '', 							// Item sales tax
					'itemurl' => '', 							// URL for the item.
					'itemweightvalue' => '', 					// The weight value of the item.
					'itemweightunit' => '', 					// The weight unit of the item.
					'itemheightvalue' => '', 					// The height value of the item.
					'itemheightunit' => '', 					// The height unit of the item.
					'itemwidthvalue' => '', 					// The width value of the item.
					'itemwidthunit' => '', 						// The width unit of the item.
					'itemlengthvalue' => '', 					// The length value of the item.
					'itemlengthunit' => '',  					// The length unit of the item.
					'itemurl' => '', 							// URL for the item.
					'itemcategory' => '', 						// Must be one of the following values:  Digital, Physical
					'ebayitemnumber' => '', 					// Auction item number.  
					'ebayitemauctiontxnid' => '', 				// Auction transaction ID number.  
					'ebayitemorderid' => '',  					// Auction order ID number.
					'ebayitemcartid' => ''						// The unique identifier provided by eBay for this order from the buyer. These parameters must be ordered sequentially beginning with 0 (for example L_EBAYITEMCARTID0, L_EBAYITEMCARTID1). Character length: 255 single-byte characters
					);
		array_push($PaymentOrderItems, $Item);
		
		// Now we've got our OrderItems for this individual payment, 
		// so we'll load them into the $Payment array
		$Payment['order_items'] = $PaymentOrderItems;
		
		// Now we add the current $Payment array into the $Payments array collection
		array_push($Payments, $Payment);
		
		$BuyerDetails = array(
								'buyerid' => '', 				// The unique identifier provided by eBay for this buyer.  The value may or may not be the same as the username.  In the case of eBay, it is different.  Char max 255.
								'buyerusername' => '', 			// The username of the marketplace site.
								'buyerregistrationdate' => ''	// The registration of the buyer with the marketplace.
								);
								
		// For shipping options we create an array of all shipping choices similar to how order items works.
		$ShippingOptions = array();
		$Option = array(
						'l_shippingoptionisdefault' => '', 				// Shipping option.  Required if specifying the Callback URL.  true or false.  Must be only 1 default!
						'l_shippingoptionname' => '', 					// Shipping option name.  Required if specifying the Callback URL.  50 character max.
						'l_shippingoptionlabel' => '', 					// Shipping option label.  Required if specifying the Callback URL.  50 character max.
						'l_shippingoptionamount' => '' 					// Shipping option amount.  Required if specifying the Callback URL.  
						);
		array_push($ShippingOptions, $Option);
			
		// For billing agreements we create an array similar to working with 
		// payments, order items, and shipping options.	
		$BillingAgreements = array();
		$Item = array(
					  'l_billingtype' => '', 							// Required.  Type of billing agreement.  For recurring payments it must be RecurringPayments.  You can specify up to ten billing agreements.  For reference transactions, this field must be either:  MerchantInitiatedBilling, or MerchantInitiatedBillingSingleSource
					  'l_billingagreementdescription' => '', 			// Required for recurring payments.  Description of goods or services associated with the billing agreement.  
					  'l_paymenttype' => '', 							// Specifies the type of PayPal payment you require for the billing agreement.  Any or IntantOnly
					  'l_billingagreementcustom' => ''					// Custom annotation field for your own use.  256 char max.
					  );
		array_push($BillingAgreements, $Item);
		
		$PayPalRequestData = array(
						'SECFields' => $SECFields, 
						'SurveyChoices' => $SurveyChoices, 
						'Payments' => $Payments, 
						'BuyerDetails' => $BuyerDetails, 
						'ShippingOptions' => $ShippingOptions, 
						'BillingAgreements' => $BillingAgreements
					);
					
		$PayPalResult = $this->paypal_pro->SetExpressCheckout($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_express_checkout_details($token)
	{			
		$PayPalResult = $this->paypal_pro->GetExpressCheckoutDetails($token);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_express_checkout_payment()
	{
		$DECPFields = array(
							'token' => '', 								// Required.  A timestamped token, the value of which was returned by a previous SetExpressCheckout call.
							'payerid' => '', 							// Required.  Unique PayPal customer id of the payer.  Returned by GetExpressCheckoutDetails, or if you used SKIPDETAILS it's returned in the URL back to your RETURNURL.
							'returnfmfdetails' => '', 					// Flag to indiciate whether you want the results returned by Fraud Management Filters or not.  1 or 0.
							'giftmessage' => '', 						// The gift message entered by the buyer on the PayPal Review page.  150 char max.
							'giftreceiptenable' => '', 					// Pass true if a gift receipt was selected by the buyer on the PayPal Review page. Otherwise pass false.
							'giftwrapname' => '', 						// The gift wrap name only if the gift option on the PayPal Review page was selected by the buyer.
							'giftwrapamount' => '', 					// The amount only if the gift option on the PayPal Review page was selected by the buyer.
							'buyermarketingemail' => '', 				// The buyer email address opted in by the buyer on the PayPal Review page.
							'surveyquestion' => '', 					// The survey question on the PayPal Review page.  50 char max.
							'surveychoiceselected' => '',  				// The survey response selected by the buyer on the PayPal Review page.  15 char max.
							'allowedpaymentmethod' => '' 				// The payment method type. Specify the value InstantPaymentOnly.
						);
						
		// You can now utlize parallel payments (split payments) within Express Checkout.
		// Here we'll gather all the payment data for each payment included in this checkout 
		// and pass them into a $Payments array.  
		
		// Keep in mind that each payment will ahve its own set of OrderItems
		// so don't get confused along the way.	
							
		$Payments = array();
		$Payment = array(
						'amt' => '', 							// Required.  The total cost of the transaction to the customer.  If shipping cost and tax charges are known, include them in this value.  If not, this value should be the current sub-total of the order.
						'currencycode' => '', 					// A three-character currency code.  Default is USD.
						'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
						'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
						'shipdiscamt' => '', 					// Shipping discount for this order, specified as a negative number.
						'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays the string 'Yes' and the insurance amount.  If true, the total shipping insurance for this order must be a positive number.
						'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
						'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
						'desc' => '', 							// Description of items on the order.  127 char max.
						'custom' => '', 						// Free-form field for your own use.  256 char max.
						'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
						'notifyurl' => '', 						// URL for receiving Instant Payment Notifications
						'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
						'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
						'shiptostreet2' => '', 					// Second street address.  100 char max.
						'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
						'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
						'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
						'shiptocountrycode' => '', 				// Required if shipping is included.  Country code of shipping address.  2 char max.
						'shiptophonenum' => '',  				// Phone number for shipping address.  20 char max.
						'notetext' => '', 						// Note to the merchant.  255 char max.  
						'allowedpaymentmethod' => '', 			// The payment method type.  Specify the value InstantPaymentOnly.
						'paymentaction' => '', 					// How you want to obtain the payment.  When implementing parallel payments, this field is required and must be set to Order. 
						'paymentrequestid' => '',  				// A unique identifier of the specific payment request, which is required for parallel payments. 
						'sellerid' => '', 						// The unique non-changing identifier for the seller at the marketplace site.  This ID is not displayed.
						'sellerusername' => '', 				// The current name of the seller or business at the marketplace site.  This name be shown to the buyer.
						'sellerregistrationdate' => '', 		// Date when the seller registered with the marketplace.
						'softdescriptor' => '', 				// A per transaction description of the payment that is passed to the buyer's credit card statement.
						'transactionid' => ''					// Tranaction identification number of the tranasction that was created.  NOTE:  This field is only returned after a successful transaction for DoExpressCheckout has occurred. 
						);
			
		// For order items you populate a nested array with multiple $Item arrays.  
		// Normally you'll be looping through cart items to populate the $Item array
		// Then push it into the $OrderItems array at the end of each loop for an entire 
		// collection of all items in $OrderItems.
					
		$PaymentOrderItems = array();
		$Item = array(
					'name' => '', 								// Item name. 127 char max.
					'desc' => '', 								// Item description. 127 char max.
					'amt' => '', 								// Cost of item.
					'number' => '', 							// Item number.  127 char max.
					'qty' => '', 								// Item qty on order.  Any positive integer.
					'taxamt' => '', 							// Item sales tax
					'itemurl' => '', 							// URL for the item.
					'itemweightvalue' => '', 					// The weight value of the item.
					'itemweightunit' => '', 					// The weight unit of the item.
					'itemheightvalue' => '', 					// The height value of the item.
					'itemheightunit' => '', 					// The height unit of the item.
					'itemwidthvalue' => '', 					// The width value of the item.
					'itemwidthunit' => '', 						// The width unit of the item.
					'itemlengthvalue' => '', 					// The length value of the item.
					'itemlengthunit' => '',  					// The length unit of the item.
					'itemurl' => '', 							// The URL for the item.
					'itemcategory' => '', 						// Must be one of the following:  Digital, Physical
					'ebayitemnumber' => '', 					// Auction item number.  
					'ebayitemauctiontxnid' => '', 				// Auction transaction ID number.  
					'ebayitemorderid' => '',  					// Auction order ID number.
					'ebayitemcartid' => ''						// The unique identifier provided by eBay for this order from the buyer. These parameters must be ordered sequentially beginning with 0 (for example L_EBAYITEMCARTID0, L_EBAYITEMCARTID1). Character length: 255 single-byte characters
					);
		array_push($PaymentOrderItems, $Item);
		
		// Now we've got our OrderItems for this individual payment, 
		// so we'll load them into the $Payment array
		$Payment['order_items'] = $PaymentOrderItems;
		
		// Now we add the current $Payment array into the $Payments array collection
		array_push($Payments, $Payment);
		
		$UserSelectedOptions = array(
									 'shippingcalculationmode' => '', 	// Describes how the options that were presented to the user were determined.  values are:  API - Callback   or   API - Flatrate.
									 'insuranceoptionselected' => '', 	// The Yes/No option that you chose for insurance.
									 'shippingoptionisdefault' => '', 	// Is true if the buyer chose the default shipping option.  
									 'shippingoptionamount' => '', 		// The shipping amount that was chosen by the buyer.
									 'shippingoptionname' => '', 		// Is true if the buyer chose the default shipping option...??  Maybe this is supposed to show the name..??
									 );
									 
		$PayPalRequestData = array(
							'DECPFields' => $DECPFields, 
							'Payments' => $Payments, 
							'UserSelectedOptions' => $UserSelectedOptions
						);
						
		$PayPalResult = $this->paypal_pro->DoExpressCheckoutPayment($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Transaction_search()
	{
		$TSFields = array(
							'startdate' => '', 							// Required.  The earliest transaction date you want returned.  Must be in UTC/GMT format.  2008-08-30T05:00:00.00Z
							'enddate' => '', 							// The latest transaction date you want to be included.
							'email' => '', 								// Search by the buyer's email address.
							'receiver' => '', 							// Search by the receiver's email address.  
							'receiptid' => '', 							// Search by the PayPal account optional receipt ID.
							'transactionid' => '', 						// Search by the PayPal transaction ID.
							'invnum' => '', 							// Search by your custom invoice or tracking number.
							'acct' => '', 								// Search by a credit card number, as set by you in your original transaction.  
							'auctionitemnumber' => '', 					// Search by auction item number.
							'transactionclass' => '', 					// Search by classification of transaction.  Possible values are: All, Sent, Received, MassPay, MoneyRequest, FundsAdded, FundsWithdrawn, Referral, Fee, Subscription, Dividend, Billpay, Refund, CurrencyConversions, BalanceTransfer, Reversal, Shipping, BalanceAffecting, ECheck
							'amt' => '', 								// Search by transaction amount.
							'currencycode' => '', 						// Search by currency code.
							'status' => '',  							// Search by transaction status.  Possible values: Pending, Processing, Success, Denied, Reversed
							'profileid' => ''							// Recurring Payments profile ID.  Currently undocumented but has tested to work.
						);
						
		$PayerName = array(
							'salutation' => '', 						// Search by payer's salutation.
							'firstname' => '', 							// Search by payer's first name.
							'middlename' => '', 						// Search by payer's middle name.
							'lastname' => '', 							// Search by payer's last name.
							'suffix' => ''	 							// Search by payer's suffix.
						);
						
		$PayPalRequestData = array(
							'TSFields' => $TSFields, 
							'PayerName' => $PayerName
						);	
						
		$PayPalResult = $this->paypal_pro->TransactionSearch($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_non_reference_credit()
	{
		$DNRCFields = array(
							'amt' => '', 						// Required.  Total of order including shipping, handling, and tax.  
							'netamt' => '', 					// Total amount of all items in this transactions.  Subtotal.
							'shippingamt' => '', 				// Total shipping costs on the transaction.
							'taxamt' => '', 					// Sum of tax for all items on the order.
							'currencycode' => '', 				// Required.  Default is USD.  Only valid values are: AUD, CAD, EUR, GBP, JPY, and USD.
							'note' => '' 						// Field used by merchant to record why this credit was issued to the buyer.
						);	
						
		$CCDetails = array(
							'creditcardtype' => '', 			// Required.  Type of credit card.  Values can be: Visa, MasterCard, Discover, Amex, Maestro, Solo
							'acct' => '', 						// Required.  Credit card number.  No spaces or punctuation.
							'expdate' => '', 					// Required.  Credit card expiration date.  MMYYYY
							'cvv2' => '', 						// Requirement determined by PayPal profile settings.  Credit Card security digits.
							'startdate' => '', 					// Mo and Yr that Maestro or Solo card was issued.  MMYYYY.
							'issuenumber' => '' 				// Isssue number of Maestro or Solo card.  
		);
		
		$PayerInfo = array(
							'email' => '', 						// Email address of payer.
							'firstname' => '', 					// Payer's first name.
							'lastname' => '' 					// Payer's last name.
						);
						
		$PayerName = array(
							'salutation' => '', 				// Buyer's salutation.  20 char max.
							'firstname' => '', 					// Buyer's first name.  25 char max.
							'middlename' => '', 				// Buyer's middle name.  25 char max.
							'lastname' => '', 					// Buyer's last name.  25 char max.
							'suffix' => ''						// Buyer's suffix.  12 char max.
						);
						
		$BillingAddress = array(
								'street' => '', 				// Required.  First street address.
								'street2' => '', 				// Second street address.
								'city' => '', 					// Required.  Name of City.
								'state' => '', 					// Required. Name of State or Province.
								'countrycode' => '', 			// Required.  Country code.
								'zip' => '', 					// Required.  Postal code of payer.
								'phonenum' => '' 				// Phone Number of payer.  20 char max.
							);
							
		$PayPalRequestData = array(
								'DNRCFields'=>$DNRCFields, 
								'CCDetails'=>$CCDetails, 
								'PayerInfo'=>$PayerInfo, 
								'PayerName' =>$PayerName, 
								'BillingAddress'=>$BillingAddress
								);
						
		$PayPalResult = $this->paypal_pro->DoNonReferenceCredit($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Do_reference_transaction()
	{
		$DRTFields = array(
					   'referenceid' => '', 						// Required.  A transaction ID from a previous purchase, such as a credit card charage using DoDirectPayment, or a billing agreement ID
					   'paymentaction' => '', 						// How you want to obtain payment.  Values are:  Authorization, Sale
					   'ipaddress' => '', 							// IP address of the buyer's browser
					   'reqconfirmshipping' => '', 					// Whether you require that the buyer's shipping address on file with PayPal be a confirmed address or not.  Values are 0/1
					   'returnfmfdetails' => '', 					// Flag to indicate whether you want the results returned by Fraud Management Filters.  Values are 0/1
					   'softdescriptor' => ''						// Per transaction description of the payment that is passed to the customer's credit card statement.
					   );
		
		$ShippingAddress = array(
								'shiptoname' => '', 							// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => '', 					// Required if shipping is included.  Country code of shipping address.  2 char max.
								'shiptophonenum' => ''						// Phone number for shipping address.  20 char max.
								);
		
		$PaymentDetails = array(
								'amt' => '', 							// Required. Total amount of the order, including shipping, handling, and tax.
								'currencycode' => '', 					// A three-character currency code.  Default is USD.
								'itemamt' => '', 						// Required if you specify itemized L_AMT fields. Sum of cost of all items in this order.  
								'shippingamt' => '', 					// Total shipping costs for this order.  If you specify SHIPPINGAMT you mut also specify a value for ITEMAMT.
								'insuranceamt' => '', 
								'shippingdiscount' => '', 
								'handlingamt' => '', 					// Total handling costs for this order.  If you specify HANDLINGAMT you mut also specify a value for ITEMAMT.
								'taxamt' => '', 						// Required if you specify itemized L_TAXAMT fields.  Sum of all tax items in this order. 
								'insuranceoptionoffered' => '', 		// If true, the insurance drop-down on the PayPal review page displays Yes and shows the amount.
								'desc' => '', 							// Description of items on the order.  127 char max.
								'custom' => '', 						// Free-form field for your own use.  256 char max.
								'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
								'notifyurl' => '', 						// URL for receiving Instant Payment Notifications
								'recurring' => ''						// Flag to indicate a recurring transaction.  Values are:  Y for recurring.  Anything other than Y is not recurring.
								);
		
		// For order items you populate a nested array with multiple $Item arrays.  Normally you'll be looping through cart items to populate the $Item 
		// array and then push it into the $OrderItems array at the end of each loop for an entire collection of all items in $OrderItems.
		
		$OrderItems = array();
		$Item		 = array(
							'l_name' => '', 							// Item name. 127 char max.
							'l_desc' => '', 
							'l_amt' => '', 								// Cost of item.
							'l_number' => '', 							// Item number.  127 char max.
							'l_qty' => '', 								// Item qty on order.  Any positive integer.
							'l_taxamt' => '', 							// Item sales tax
							'l_itemweightvalue' => '', 					// The weight value of the item.
							'l_itemweightunit' => '', 					// The weight unit of the item.
							'l_itemheightvalue' => '', 					// The height value of the item.
							'l_itemheightunit' => '', 					// The height unit of the item.
							'l_itemwidthvalue' => '', 					// The width value of the item.
							'l_itemwidthunit' => '', 					// The width unit of the item.
							'l_itemlengthvalue' => '', 					// The length value of the item.
							'l_itemlengthunit' => '',  					// The length unit of the item.
							'l_ebayitemnumber' => '', 					// Auction item number.  
							'l_ebayitemauctiontxnid' => '', 			// Auction transaction ID number.  
							'l_ebayitemorderid' => '' 					// Auction order ID number.
							);
							
		array_push($OrderItems, $Item);
		
		$CCDetails = array(
							'creditcardtype' => '', 					// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
							'acct' => '', 								// Required.  Credit card number.  No spaces or punctuation.  
							'expdate' => '', 							// Required.  Credit card expiration date.  Format is MMYYYY
							'cvv2' => '', 								// Requirements determined by your PayPal account settings.  Security digits for credit card.
							'startdate' => '', 							// Month and year that Maestro or Solo card was issued.  MMYYYY
							'issuenumber' => ''							// Issue number of Maestro or Solo card.  Two numeric digits max.
						);
		
		$PayerInfo = array(
							'email' => '', 								// Email address of payer.
							'firstname' => '', 							// Unique PayPal customer ID for payer.
							'lastname' => ''						// Status of payer.  Values are verified or unverified
						);
						
		$BillingAddress = array(
								'street' => '', 						// Required.  First street address.
								'street2' => '', 						// Second street address.
								'city' => '', 							// Required.  Name of City.
								'state' => '', 							// Required. Name of State or Province.
								'countrycode' => '', 					// Required.  Country code.
								'zip' => '', 							// Required.  Postal code of payer.
								'phonenum' => '' 						// Phone Number of payer.  20 char max.
							);
							
		$PayPalRequestData = array(
							'DRTFields' => $DRTFields, 
							'ShippingAddress' => $ShippingAddress, 
							'PaymentDetails' => $PaymentDetails, 
							'OrderItems' => $OrderItems, 
							'CCDetails' => $CCDetails, 
							'PayerInfo' => $PayerInfo, 
							'BillingAddress' => $BillingAddress
						);	
						
		$PayPalResult = $this->paypal_pro->DoReferenceTransaction($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_balance()
	{		
		$GBFields = array('returnallcurrencies' => '1');
		$PayPalRequestData = array('GBFields'=>$GBFields);
		$PayPalResult = $this->paypal_pro->GetBalance($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('get_balance',$data);
		}
	}
	
	
	function Get_pal_details()
	{
		$PayPalResult = $this->paypal_pro->GetPalDetails();
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Address_verify()
	{
		$AVFields = array
					(
					'email' => '', 							// Required. Email address of PayPal member to verify.
					'street' => '', 						// Required. First line of the postal address to verify.  35 char max.
					'zip' => ''								// Required.  Postal code to verify.  
					);
					
		$PayPalRequestData = array('AVFields' => $AVFields);
		
		$PayPalResult = $this->paypal_pro->AddressVerify($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Manage_pending_transaction_status()
	{
		$MPTSFields = array
					(
					'transactionid' => '', 								// Required. Transaction ID of the payment transaction.
					'action' => ''										// Required.  The operation you want to perform on the pending transaction.  Options are: Accept, Deny 
					);
					
		$PayPalRequestData = array('MPTSFields' => $MPTSFields);
		
		$PayPalResult = $this->paypal_pro->ManagePendingTransactionStatus($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Create_recurring_payments_profile()
	{
		$CRPPFields = array(
					'token' => '', 								// Token returned from PayPal SetExpressCheckout.  Can also use token returned from SetCustomerBillingAgreement.
						);
						
		$ProfileDetails = array(
							'subscribername' => '', 					// Full name of the person receiving the product or service paid for by the recurring payment.  32 char max.
							'profilestartdate' => '', 					// Required.  The date when the billing for this profiile begins.  Must be a valid date in UTC/GMT format.
							'profilereference' => '' 					// The merchant's own unique invoice number or reference ID.  127 char max.
						);
						
		$ScheduleDetails = array(
							'desc' => '', 								// Required.  Description of the recurring payment.  This field must match the corresponding billing agreement description included in SetExpressCheckout.
							'maxfailedpayments' => '', 					// The number of scheduled payment periods that can fail before the profile is automatically suspended.  
							'autobilloutamt' => '' 						// This field indiciates whether you would like PayPal to automatically bill the outstanding balance amount in the next billing cycle.  Values can be: NoAutoBill or AddToNextBilling
						);
						
		$BillingPeriod = array(
							'trialbillingperiod' => '', 
							'trialbillingfrequency' => '', 
							'trialtotalbillingcycles' => '', 
							'trialamt' => '', 
							'billingperiod' => '', 						// Required.  Unit for billing during this subscription period.  One of the following: Day, Week, SemiMonth, Month, Year
							'billingfrequency' => '', 					// Required.  Number of billing periods that make up one billing cycle.  The combination of billing freq. and billing period must be less than or equal to one year. 
							'totalbillingcycles' => '', 				// the number of billing cycles for the payment period (regular or trial).  For trial period it must be greater than 0.  For regular payments 0 means indefinite...until canceled.  
							'amt' => '', 								// Required.  Billing amount for each billing cycle during the payment period.  This does not include shipping and tax. 
							'currencycode' => '', 						// Required.  Three-letter currency code.
							'shippingamt' => '', 						// Shipping amount for each billing cycle during the payment period.
							'taxamt' => '' 								// Tax amount for each billing cycle during the payment period.
						);
						
		$ActivationDetails = array(
							'initamt' => '', 							// Initial non-recurring payment amount due immediatly upon profile creation.  Use an initial amount for enrolment or set-up fees.
							'failedinitamtaction' => '', 				// By default, PayPal will suspend the pending profile in the event that the initial payment fails.  You can override this.  Values are: ContinueOnFailure or CancelOnFailure
						);
						
		$CCDetails = array(
							'creditcardtype' => '', 					// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
							'acct' => '', 								// Required.  Credit card number.  No spaces or punctuation.  
							'expdate' => '', 							// Required.  Credit card expiration date.  Format is MMYYYY
							'cvv2' => '', 								// Requirements determined by your PayPal account settings.  Security digits for credit card.
							'startdate' => '', 							// Month and year that Maestro or Solo card was issued.  MMYYYY
							'issuenumber' => ''							// Issue number of Maestro or Solo card.  Two numeric digits max.
						);
						
		$PayerInfo = array(
							'email' => '', 								// Email address of payer.
							'payerid' => '', 							// Unique PayPal customer ID for payer.
							'payerstatus' => '', 						// Status of payer.  Values are verified or unverified
							'business' => '' 							// Payer's business name.
						);
						
		$PayerName = array(
							'salutation' => '', 						// Payer's salutation.  20 char max.
							'firstname' => '', 							// Payer's first name.  25 char max.
							'middlename' => '', 						// Payer's middle name.  25 char max.
							'lastname' => '', 							// Payer's last name.  25 char max.
							'suffix' => ''								// Payer's suffix.  12 char max.
						);
						
		$BillingAddress = array(
								'street' => '', 						// Required.  First street address.
								'street2' => '', 						// Second street address.
								'city' => '', 							// Required.  Name of City.
								'state' => '', 							// Required. Name of State or Province.
								'countrycode' => '', 					// Required.  Country code.
								'zip' => '', 							// Required.  Postal code of payer.
								'phonenum' => '' 						// Phone Number of payer.  20 char max.
							);
							
		$ShippingAddress = array(
								'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => '', 				// Required if shipping is included.  Country code of shipping address.  2 char max.
								'shiptophonenum' => ''					// Phone number for shipping address.  20 char max.
								);
								
		$PayPalRequestData = array(
							'CRPPFields' => $CRPPFields, 
							'ProfileDetails' => $ProfileDetails, 
							'ScheduleDetails' => $ScheduleDetails, 
							'BillingPeriod' => $BillingPeriod, 
							'ActivationDetails' => $ActivationDetails, 
							'CCDetails' => $CCDetails, 
							'PayerInfo' => $PayerInfo, 
							'PayerName' => $PayerName, 
							'BillingAddress' => $BillingAddress, 
							'ShippingAddress' => $ShippingAddress
						);	
						
		$PayPalResult = $this->paypal_pro->CreateRecurringPaymentsProfile($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_recurring_payments_profile_details()
	{
		$GRPPDFields = array(
					   'profileid' => ''			// Profile ID of the profile you want to get details for.
					   );
					   
		$PayPalRequestData = array('GRPPDFields' => $GRPPDFields);
		
		$PayPalResult = $this->paypal_pro->GetRecurringPaymentsProfileDetails($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Manage_recurring_payments_profile_status()
	{
		$MRPPSFields = array(
						'profileid' => '', 				// Required. Recurring payments profile ID returned from CreateRecurring...
						'action' => '', 				// Required. The action to be performed.  Mest be: Cancel, Suspend, Reactivate
						'note' => ''					// The reason for the change in status.  For express checkout the message will be included in email to buyers.  Can also be seen in both accounts in the status history.
						);
						
		$PayPalRequestData = array('MRPPSFields' => $MRPPSFields);
		
		$PayPalResult = $this->paypal_pro->ManageRecurringPaymentsProfileStatus($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Bill_outstanding_amount()
	{
		$BOAFields = array(
							   'profileid' => '', 				// Required.  Recurring payments profile ID returned from CreateRecurringPaymentsProfile.
							   'amt' => '', 					// The amount to bill.  Must be less than or equal to the current oustanding balance.  Default is to collect entire amount.
							   'note' => ''						// Note about the reason for the non-scheduled payment.  EC profiles will show this message in the email notification to the buyer and can be seen in the details page by both buyer and seller.
							   );
							   
		$PayPalRequestData = array('BOAFields' => $BOAFields);
		
		$PayPalResult = $this->paypal_pro->BillOutstandingAmount($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Update_recurring_payments_profile()
	{
		$URPPFields = array(
						   'profileid' => '', 							// Required.  Recurring payments ID.
						   'note' => '', 								// Note about the reason for the update to the profile.  Included in EC profile notification emails and in details pages.
						   'desc' => '', 								// Description of the recurring payment profile.
						   'subscribername' => '', 						// Full name of the person receiving the product or service paid for by the recurring payment profile.
						   'profilereference' => '', 					// The merchant's own unique reference or invoice number.
						   'additionalbillingcycles' => '', 			// The number of additional billing cycles to add to this profile.
						   'amt' => '', 								// Billing amount for each cycle in the subscription, not including shipping and tax.  Express Checkout profiles can only be updated by 20% every 180 days.
						   'shippingamt' => '', 						// Shipping amount for each billing cycle during the payment period.
						   'taxamt' => '',  							// Tax amount for each billing cycle during the payment period.
						   'outstandingamt' => '', 						// The current past-due or outstanding amount.  You can only decrease this amount.  
						   'autobilloutamt' => '', 						// This field indiciates whether you would like PayPal to automatically bill the outstanding balance amount in the next billing cycle.
						   'maxfailedpayments' => '', 					// The number of failed payments allowed before the profile is automatically suspended.  The specified value cannot be less than the current number of failed payments for the profile.
						   'profilestartdate' => ''						// The date when the billing for this profile begins.  UTC/GMT format.
						   );
		
		$BillingAddress = array(
							'street' => '', 						// Required.  First street address.
							'street2' => '', 						// Second street address.
							'city' => '', 							// Required.  Name of City.
							'state' => '', 							// Required. Name of State or Province.
							'countrycode' => '', 					// Required.  Country code.
							'zip' => '', 							// Required.  Postal code of payer.
							'phonenum' => '' 						// Phone Number of payer.  20 char max.
						);
		
		$ShippingAddress = array(
							'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
							'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
							'shiptostreet2' => '', 					// Second street address.  100 char max.
							'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
							'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
							'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
							'shiptocountry' => '', 				// Required if shipping is included.  Country code of shipping address.  2 char max.
							'shiptophonenum' => ''					// Phone number for shipping address.  20 char max.
							);
		
		$BillingPeriod = array(
						'trialbillingperiod' => '', 
						'trialbillingfrequency' => '', 
						'trialtotalbillingcycles' => '', 
						'trialamt' => '', 
						'billingperiod' => '', 						// Required.  Unit for billing during this subscription period.  One of the following: Day, Week, SemiMonth, Month, Year
						'billingfrequency' => '', 					// Required.  Number of billing periods that make up one billing cycle.  The combination of billing freq. and billing period must be less than or equal to one year. 
						'totalbillingcycles' => '', 				// the number of billing cycles for the payment period (regular or trial).  For trial period it must be greater than 0.  For regular payments 0 means indefinite...until canceled.  
						'amt' => '', 								// Required.  Billing amount for each billing cycle during the payment period.  This does not include shipping and tax. 
						'currencycode' => '', 						// Required.  Three-letter currency code.
					);
		
		$CCDetails = array(
						'creditcardtype' => '', 					// Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
						'acct' => '', 								// Required.  Credit card number.  No spaces or punctuation.  
						'expdate' => '', 							// Required.  Credit card expiration date.  Format is MMYYYY
						'cvv2' => '', 								// Requirements determined by your PayPal account settings.  Security digits for credit card.
						'startdate' => '', 							// Month and year that Maestro or Solo card was issued.  MMYYYY
						'issuenumber' => ''							// Issue number of Maestro or Solo card.  Two numeric digits max.
					);
		
		$PayerInfo = array(
						'email' => '', 								// Payer's email address.
						'firstname' => '', 							// Required.  Payer's first name.
						'lastname' => ''							// Required.  Payer's last name.
					);	
					
		$PayPalRequestData = array(
							'URPPFields' => $URPPFields, 
							'BillingAddress' => $BillingAddress, 
							'ShippingAddress' => $ShippingAddress, 
							'BillingPeriod' => $BillingPeriod, 
							'CCDetails' => $CCDetails, 
							'PayerInfo' => $PayerInfo
						);
						
		$PayPalResult = $this->paypal_pro->UpdateRecurringPaymentsProfile($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Billing_agreement_update()
	{
		$BAUFields = array(
						   'referenceid' => '', 							// Required. An ID, such as a billing agreement ID or a reference transaction ID that is associated with a billing agreement.
						   'billingagreementstatus' => '', 					// The current status of the billing agreement, which is one of the following values: Active or Canceled.
						   'billingagreementdescription' => '', 			// Description of goods or services associated with the billing agreement, which is required for each recurring payment billing agreement. PayPal recommends that the description contain a brief summary of the billing agreement terms and conditions. For example, customer will be billed at "9.99 per month for 2 years". 127 Car max.
						   'billingagreementcustom' => ''					// Custom annotation field for your own use.  256 char max.
						   );
						   
		$PayPalRequestData = array('BAUFields' => $BAUFields);	
		
		$PayPalResult = $this->paypal_pro->BillingAgreementUpdate($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Set_mobile_checkout()
	{
		$SMCFields = array(
							'phonecountrycode' => '', 				// Three-digit country code for buyer's phone number.  
							'phonenum' => '', 						// Localized phone number used by the buyer to submit the payment request.  if the phone number is activated for mobile checkout, PayPal uses this value to pre-fill the PayPal login page.
							'amt' => '', 							// Required. Cost of item before tax and shipping.
							'currencycode' => '', 					// Required.  Three-character currency code.  Default is USD.
							'taxamt' => '', 						// Tax on item purchased.
							'shippingamt' => '', 					// shipping costs for this transaction.
							'desc' => '', 							// Required. The name of the item is being ordered.  127 char max.
							'number' => '', 						// Pass-through field allowing you to specify detailis, such as a SKU.  127 char max.
							'custom' => '', 						// Free-form field for your own use.  256 char max.
							'invnum' => '', 						// Your own invoice or tracking number.  127 char max.
							'returnurl' => '', 						// URL to direct the browser to after leaving PayPal pages.
							'cancelurl' => '', 						// URL to direct the borwser to if the user cancels payment.
							'addressdisplay' => '', 				// Indiciates whether or not a shipping address is required.  1 or 0. 
							'sharephonenum' => '', 					// Indiciates whether or not the customer's phone number is returned to the merchant.  1 or 0.  
							'email' => '' 							// Email address of the buyer as entered during checkout.  If the phone number is not activated for Mobile Checkout, PayPal uses this value to pre-fill the PayPal login page.  127 char max.
						);
						
		$ShippingAddress = array(
								'shiptoname' => '', 					// Required if shipping is included.  Person's name associated with this address.  32 char max.
								'shiptostreet' => '', 					// Required if shipping is included.  First street address.  100 char max.
								'shiptostreet2' => '', 					// Second street address.  100 char max.
								'shiptocity' => '', 					// Required if shipping is included.  Name of city.  40 char max.
								'shiptostate' => '', 					// Required if shipping is included.  Name of state or province.  40 char max.
								'shiptozip' => '', 						// Required if shipping is included.  Postal code of shipping address.  20 char max.
								'shiptocountry' => '' 					// Required if shipping is included.  Country code of shipping address.  2 char max.
								);
								
		$PayPalRequestData = array(
							'SMCFields' => $SMCFields, 
							'ShippingAddress' => $ShippingAddress
						);	
						
		$PayPalResult = $this->paypal_pro->SetMobileCheckout($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Do_mobile_checkout_payment()
	{
		$DMCFields = array(
						   'token' => ''				// Token returned by SetMobileCheckout
						   );
						   
		$PayPalRequestData = array('DMCFields' => $DMCFields);
		
		$PayPalResult = $this->paypal_pro->DoMobileCheckoutPayment($PayPalRequestData);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Set_auth_flow_param()
	{
		$SetAuthFlowParamFields = array(
										'ReturnURL' => '', 														// URL to which the customer's browser is returned after choosing to authenticate with PayPal
										'CancelURL' => '', 														// URL to which the customer is returned if they decide not to log in.
										'LogoutURL' => '', 														// URL to which the customer is returned after logging out from your site.
										'LocalCode' => '', 														// Local of pages displayed by PayPal during authentication.  AU, DE, FR, IT, GB, ES, US
										'PageStyle' => '', 														// Sets the custom payment page style of the PayPal pages associated with this button/link.
										'HDRIMG' => '', 														// URL for the iamge you want to appear at the top of the PayPal pages.  750x90.  Should be stored on a secure server.  127 char max.
										'HDRBorderColor' => '', 												// Sets the border color around the header on PayPal pages.HTML Hexadecimal value.
										'HDRBackColor' => '', 													// Sets the background color for PayPal pages.
										'PayFlowColor' => '', 													// Sets the background color for the payment page.
										'InitFlowType' => '', 													// The initial flow type, which is one of the following:  login  / signup   Default is login.
										'FirstName' => '', 														// Customer's first name.
										'LastName' => '',  														// Customer's last name.
										'ServiceName1' => 'Name', 
										'ServiceName2' => 'Email', 
										'ServiceDefReq1' => 'Required', 
										'ServiceDefReq2' => 'Required'
										);
		
		$ShippingAddress = array(
								'ShipToName' => '', 													// Persona's name associated with this address.
								'ShipToStreet' => '', 													// First street address.
								'ShipToStreet2' => '', 													// Second street address.
								'ShipToCity' => '', 													// Name of city.
								'ShipToState' => '', 													// Name of State or Province.
								'ShipToZip' => '', 														// US Zip code or other country-specific postal code.
								'ShipToCountryCode' => '' 												// Country code.
								 );
								 
		$PayPalRequestData = array(
							'SetAuthFlowParamFields' => $SetAuthFlowParamFields, 
							'ShippingAddress' => $ShippingAddress
						);	
						
		$PayPalResult = $this->paypal_pro->SetAuthFlowParam($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Get_auth_details($token)
	{
		$PayPalResult = $this->paypal_pro->GetAuthDetails($token);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Get_access_permissions_details($token)
	{
		$PayPalResult = $this->paypal_pro->GetAccessPermissionsDetails($token);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Set_access_permissions()
	{
		$SetAccessPermissionsFields = array(
											'ReturnURL' => '', 														// URL to return the browser to after authorizing permissions.
											'CancelURL' => '', 													 	// URL to return if the customer cancels authorization
											'LogoutURL' => '', 														// URL to return to on logout from PayPal
											'LocalCode' => '', 														// Local of pages displayed by PayPal during authentication.  AU, DE, FR, IT, GB, ES, US
											'PageStyle' => '', 														// Sets the custom payment page style of the PayPal pages associated with this button/link.
											'HDRIMG' => '', 														// URL for the iamge you want to appear at the top of the PayPal pages.  750x90.  Should be stored on a secure server.  127 char max.
											'HDRBorderColor' => '', 												// Sets the border color around the header on PayPal pages.HTML Hexadecimal value.
											'HDRBackColor' => '', 													// Sets the background color for PayPal pages.
											'PayFlowColor' => '', 													// Sets the background color for the payment page.
											'InitFlowType' => '', 													// The initial flow type, which is one of the following:  login  / signup   Default is login.
											'FirstName' => '', 														// Customer's first name.
											'LastName' => ''
											);
		
		$RequiredPermissions = array(
									 'Email', 
									 'Name', 
									 'GetBalance', 
									 'RefundTransaction', 
									 'GetTransactionDetails', 
									 'TransactionSearch', 
									 'MassPay', 
									 'EncryptedWebsitePayments', 
									 'GetExpressCheckoutDetails', 
									 'SetExpressCheckout', 
									 'DoExpressCheckoutPayment', 
									 'DoCapture', 
									 'DoAuthorization', 
									 'DoReauthorization', 
									 'DoVoid', 
									 'DoDirectPayment', 
									 'SetMobileCheckout', 
									 'CreateMobileCheckout', 
									 'DoMobileCheckoutPayment', 
									 'DoUATPAuthorization', 
									 'DoUATPExpressCheckoutPayment', 
									 'GetBillingAgreementCustomerDetails', 
									 'SetCustomerBillingAgreement', 
									 'CreateBillingAgreement', 
									 'BillAgreementUpdate', 
									 'BillUser', 
									 'DoReferenceTransaction', 
									 'Express_Checkout', 
									 'Admin_API', 
									 'Auth_Settle', 
									 'Transaction_History'
									 );
		
		$OptionalPermissions = array(
									 'Email', 
									 'Name', 
									 'GetBalance', 
									 'RefundTransaction', 
									 'GetTransactionDetails', 
									 'TransactionSearch', 
									 'MassPay', 
									 'EncryptedWebsitePayments', 
									 'GetExpressCheckoutDetails', 
									 'SetExpressCheckout', 
									 'DoExpressCheckoutPayment', 
									 'DoCapture', 
									 'DoAuthorization', 
									 'DoReauthorization', 
									 'DoVoid', 
									 'DoDirectPayment', 
									 'SetMobileCheckout', 
									 'CreateMobileCheckout', 
									 'DoMobileCheckoutPayment', 
									 'DoUATPAuthorization', 
									 'DoUATPExpressCheckoutPayment', 
									 'GetBillingAgreementCustomerDetails', 
									 'SetCustomerBillingAgreement', 
									 'CreateBillingAgreement', 
									 'BillAgreementUpdate', 
									 'BillUser', 
									 'DoReferenceTransaction', 
									 'Express_Checkout', 
									 'Admin_API', 
									 'Auth_Settle', 
									 'Transaction_History'
									 );
		
		$PayPalRequestData = array(
								   'SetAccessPermissionsFields' => $SetAccessPermissionsFields, 
								   'RequiredPermissions' => $RequiredPermissions, 
								   'OptionalPermissions' => $OptionalPermissions
								   );	
								   
		$PayPalResult = $this->paypal_pro->SetAccessPermissions($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Update_access_permissions($payer_id)
	{
		$PayPalResult = $this->paypal_pro->UpdateAccessPermissions($payer_id);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
	
	
	function Create_billing_agreement($token = "")
	{
		$PayPalResult = $this->paypal_pro->CreateBillingAgreement($token);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Set_customer_billing_agreement()
	{
		// Prepare request arrays
		$SCBAFields = array(
							'returnurl' => '', 									// Required.  URL to which the customer's browser is returned after chooing to pay with PayPal.
							'cancelurl' => '', 									// Required.  URL to which the customer is returned if he does not approve the use of PayPal to pay you.
							'localcode' => '', 									// Local of pages displayed by PayPal during checkout.  
							'pagestyle' => '', 									// Sets the custom payment page style for payment pages associated with this button/link.
							'hdrimg' => '', 									// A URL for the image you want to appear at the top, left of the payment page.  Max size 750 x 90
							'hdrbordercolor' => '', 							// Sets the border color around the header of the payment page.
							'hdrbackcolor' => '', 								// Sets the background color for the header of the payments page.
							'payflowcolor' => '', 								// Sets the background color for the payment page.
							'email' => ''										// Email address of the buyer as entered during checkout.  Will be pre-filled if included.
							);	
							
		$BillingAgreements = array();
		$Item = array(
					  'l_billingtype' => '', 							// Required.  Type of billing agreement.  For recurring payments it must be RecurringPayments.  You can specify up to ten billing agreements.  For reference transactions, this field must be either:  MerchantInitiatedBilling, or MerchantInitiatedBillingSingleSource
					  'l_billingagreementdescription' => '', 			// Required for recurring payments.  Description of goods or services associated with the billing agreement.  
					  'l_paymenttype' => '', 							// Specifies the type of PayPal payment you require for the billing agreement.  Any or IntantOnly
					  'l_billingagreementcustom' => ''					// Custom annotation field for your own use.  256 char max.
					  );
		array_push($BillingAgreements, $Item);
		
		$PayPalRequestData = array(
								'SCBAFields' => $SCBAFields, 
								'BillingAgreements' => $BillingAgreements
								);
		
		// Pass data into class for processing with PayPal and load the response array into $PayPalResult
		$PayPalResult = $this->paypal_pro->SetCustomerBillingAgreement($PayPalRequestData);
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}		
	}
	
	
	function Get_billing_agreement_customer_details($token = "")
	{
		$PayPalResult = $this->paypal_pro->GetBillingAgreementCustomerDetails($Token);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}	
	}
	
	
	function Bm_button_search()
	{
		$BMButtonSearchFields = array
								(
								'startdate' => '', 			// Required.  Starting date for the search.  UTC/GMT format: 2009-08-24T05:38:48Z
								'enddate' => ''				// Ending date for the search.  UTC/GMT format: 2010-05-01T05:38:48Z  
								);
								
		$PayPalRequestData = array('BMButtonSearchFields'=>$BMButtonSearchFields);
						
		$PayPalResult = $this->paypal_pro->BMButtonSearch($PayPalRequestData);	
		
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK']))
		{
			$errors = array('Errors'=>$PayPalResult['ERRORS']);
			$this->load->view('paypal_error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.	
		}
	}
}

/* End of file payments_pro.php */
/* Location: ./system/application/controllers/payments_pro.php */