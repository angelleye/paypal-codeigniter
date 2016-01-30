<html>
<head>
<title>Angell EYE PayPal Adaptive Payments Pro CodeIgniter Library Demo</title>

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
 font-size: 16px;
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
</head>
<body>

<p><a href="/paypal/samples/demo"><img src="https://www.angelleye.com/images/paypal-codeigniter-library-demo-header.png" width="750" height="90"></a></p>
<h1>Angell EYE PayPal CodeIgniter Library Demo	</h1>
<p>This library is written to reflect PayPal's documentation directly. It's very simple to use with a quick understanding of the way it's designed.</p>
<ul>
  <li>Every PayPal API method is included as a method within the library and also within the controller.  </li>
  <li>The controller methods include &quot;templates&quot; that consists of every possible parameter that you can pass into that particular API call with PayPal.</li>
  <li>Simply fill in the empty parameters and pass that into the library.</li>
  <li>The library will handle all communication with PayPal, data parsing, etc. and return an array of results.
    <ul>
      <li>All actual response fields from PayPal</li>
      <li>All request fields split up into individual fields for ease of troubleshooting</li>
      <li>Raw request and response fields</li>
    </ul>
  </li>
</ul>
<p>The package consists of 3 libraries:</p>
<ul>
  <li><a href="<?php echo site_url('paypal/samples/payments_pro'); ?>">Payments Pro</a></li>
  <li><a href="<?php echo site_url('paypal/samples/adaptive_payments'); ?>">Adaptive Payments</a></li>
  <li><a href="<?php echo site_url('paypal/samples/payflow'); ?>">PayFlow</a></li>
</ul>
<p><a href="http://angelleye.com/how-to-integrate-paypal-with-codeigniter-php-class-library/" target="_blank">View More Detailed Documentation</a></p>
</body>
</html>
