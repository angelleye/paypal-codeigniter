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
</head>
<body>

<p><a href="/paypal/samples/demo"><img src="https://www.angelleye.com/images/paypal-codeigniter-library-demo-header.png" width="750" height="90"></a></p>
<h1>Angell EYE PayPal CodeIgniter Library Demo	</h1>
<p>This library is written to reflect PayPal's Classic API documentation directly. It's very simple to use with a quick understanding of the way it's designed.</p>
<ul>
  <li>Every PayPal API call is included as a method within the library and also within the template controllers.
    <ul>
      <li>The templates provide a starting point for any PayPal API call you want to make. 
        <ul>
          <li>The templates can be found at <strong>/application/controllers/paypal/templates/</strong>.</li>
        </ul>
      </li>
      <ul>
        <li>Each one consists of every possible parameter that you can pass into that particular API call.</li>
        <li>Each parameter has comments pulled from PayPal's API documentation to provide details about that parameter.</li>
      </ul>
    </ul>
  </li>
  <li>Simply fill in the empty parameters and the library handles the rest.</li>
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
<p>Click the links for functional samples of API calls from these libraries.</p>
<p><a href="http://angelleye.com/how-to-integrate-paypal-with-codeigniter-php-class-library/" target="_blank">View More Detailed Documentation</a> | <a href="https://www.angelleye.com/product-category/php-class-libraries/demo-kits/" target="_blank">Get Fully Functional Demo Kits</a></p>
</body>
</html>
