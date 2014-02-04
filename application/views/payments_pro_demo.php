<html>
<head>
<title>Angell EYE PayPal Payments Pro CodeIgniter Library Demo</title>

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
}

</style>
</head>
<body>

<p><a href="http://www.angelleye.com"><img src="/demo/codeigniter/images/logo.jpg" alt="Angell EYE Web Solutions Made Easy" width="500" height="102" border="0"></a></p>
<h1>Angell EYE PayPal Payments Pro CodeIgniter Library Demo	</h1>
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
<p>There are two methods within the controller that are setup for demonstration purposes. I'll go over those here.</p>
<h2>GetBalance</h2>
<p>This call is extremely straight forward and simply returns the current account balance for the requesting API caller. The controller already has the following method included:</p>
<p><?php highlight_file($_SERVER['DOCUMENT_ROOT'].'/demo/codeigniter/includes/get_balance.php'); ?></p>
<p><a href="/demo/codeigniter/paypal/payments_pro/get_balance" target="_blank">Run the code above and see the output.</a></p>
<h2>DoDirectPayment</h2>
<p>This call allows you to process credit cards directly using PayPal Payments Pro. The controller has a method called Do_direct_payment_demo() that is displayed here:</p>
<p>
  <?php highlight_file($_SERVER['DOCUMENT_ROOT'].'/demo/codeigniter/includes/do_direct_payment.php'); ?>
</p>
<p><a href="/demo/codeigniter/paypal/payments_pro/do_direct_payment_demo" target="_blank">Run the code above and see the output.</a></p>
</body>
</html>