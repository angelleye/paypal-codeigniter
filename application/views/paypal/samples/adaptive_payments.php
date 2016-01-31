<html>
<head>
<title>Angell EYE PayPal Adaptive Payments CodeIgniter Library Demo</title>

<style type="text/css">

body {
 background-color: #fff;
 margin: 40px;
 font-family: Lucida Grande, Verdana, Sans-serif;
 font-size: 14px;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 20px;
 font-weight: bold;
 margin: 24px 0 2px 0;
 padding: 5px 0 6px 0;
}

code {
 font-family: Monaco, Verdana, Sans-serif;
 font-size: 12px;
 background-color: #f9f9f9;
 border: 1px solid #D0D0D0;
 color: #002166;
 display: block;
 margin: 14px 0 14px 0;
 padding: 12px 10px 12px 10px;
 overflow:auto;
}
</style>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/highlight.min.js"></script>
<script>hljs.initHighlightingOnLoad();</script>

</head>
<body>

<p><a href="/paypal/samples/demo"><img src="https://www.angelleye.com/images/paypal-codeigniter-library-demo-header.png" width="750" height="90"></a></p>
<h1>Angell EYE PayPal Adaptive Payments CodeIgniter Library Samples</h1>
<p>The following samples demonstrate how to work with this library. </p>
<h2>ConvertCurrency</h2>
<p>This call is very straight forward and simply returns the converted currency values for the requested amount. </p>
<p>The fully functional sample code displayed here is found in <strong>/application/controllers/paypal/samples/Adaptive_payments.php</strong>. </p>
<p>An empty version of this function can be found in <strong>/application/controlles/paypal/templates/Adaptive_payments.php</strong>.</p>
<p><pre><code>
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

    </code></pre>
</p>
<p><a href="<?php echo site_url('paypal/samples/adaptive_payments/convert_currency'); ?>" target="_blank">Run the code above and see the output.</a></p>
<h2>Pay (Chained)</h2>
<p>This is a simple demonstration of generating a Chained Payment with PayPal's Adaptive Payments system. </p>
<p>You will need to <a href="https://www.angelleye.com/create-paypal-sandbox-account/" target="_blank">create a sandbox buyer account</a> to sign in so you can see the entire flow. Alternatively, you can use these buyer credentials:</p>
<p>Email Address: paypalphp@angelleye.com<br>
  Password:  paypalphp
</p>
<p>When you click the link below, the sample will return a dump of the result as well as a link to continue to PayPal. Click the link to move forward with payment. </p>
<p>Again, you'll need your own PayPal developer account and sandbox accounts to really be able to see exactly how all of this would look within each account, but the code sample shown processes the chained payment successfully.</p>
<p>The fully functional sample code displayed here is found in <strong>/application/controllers/paypal/samples/Adaptive_payments.php</strong>. </p>
<p>An empty version of this function can be found in <strong>/application/controlles/paypal/templates/Adaptive_payments.php</strong>.</p>
<p><pre><code>
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
                    echo '&lt;pre /&gt;';
                    print_r($PayPalResult);
                    echo '&lt;p&gt;&lt;a href="' . $PayPalResult['RedirectURL'] . '"&gt;PROCEED TO PAYPAL&lt;/a&gt;&lt;/p&gt;';
                }
            }
    </code></pre>
</p>
<p><a href="<?php echo site_url('paypal/samples/adaptive_payments/pay_chained'); ?>" target="_blank">Run the code above and see the output.</a></p>
<p>&nbsp;</p>
</body>
</html>