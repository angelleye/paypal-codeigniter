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
		$this->load->view('paypal/samples/payments_pro');
	}

	
	function Do_direct_payment()
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
			$this->load->view('paypal/samples/error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('paypal/samples/do_direct_payment',$data);
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
			$this->load->view('paypal/samples/error',$errors);
		}
		else
		{
			// Successful call.  Load view or whatever you need to do here.
			$data = array('PayPalResult'=>$PayPalResult);
			$this->load->view('paypal/samples/get_balance',$data);
		}
	}

	
}

/* End of file Payments_pro.php */
/* Location: ./system/application/controllers/paypal/samples/Payments_pro.php */