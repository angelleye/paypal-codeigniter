==========================
PayPal CodeIgniter Library
==========================

************
Introduction
************

This PHP class library for PayPal makes it easy to integrate nearly every PayPal API they provide.

All of the web services included in PayPal's NVP documentation are included, as well as Adaptive Accounts, 
Adaptive Payments, Permissions, Invoicing, PayFlow, and more.

*******************
Server Requirements
*******************

-  PHP version 5.2.4 or newer.
-  CodeIgniter 2.0+

************
Installation
************

Merge the /application directory into your CodeIgniter /application directory.

Open /application/config/paypal-sample.php and adjust the value with your own Sandbox and Production API credentials.  Then save-as paypal.php.

There are detailed comments inside the config file to help you fill it out correctly.

****************************
How to Make PayPal API Calls
****************************

Open /application/controllers/paypal/templates/Controller_name.php depending on which PayPal API you're going to be using.

Example:  If we want to make a call to the RefundTransaction API we open Payments_pro.php and then seek out the Refund_transaction() function.

You can build directly into the template functions if you want to, however, I recommend you setup your own controller and copy the functions there.  This keeps the templates clean for future reference.  Just remember to load the PayPal config file and library in your constructor the same way the samples and templates do.

Each template controller file includes functions with PHP arrays for every parameter available to that particular API. Simply fill in the array parameters with your own dynamic (or static) data. This data may come from...

- Session Variables
- PHP Variables
- Database Recordsets
- Static Values
- Etc.

When you run the function you will get a $PayPalResult array that consists of all the response parameters from PayPal, original request parameters sent to PayPal, and raw request/response info for troubleshooting.

If errors occur they will be available in $PayPalResult['ERRORS'].

*******
Samples
*******

After installing the library into CodeIgniter, the demo can be loaded at {base_url}/paypal/samples/demo/.  This allows you to run the fully functional samples that are included with the library.

The samples that the demo loads can be found at /application/controllers/paypal/samples/.

The Payments Pro sample controller has two methods ready for demo purposes after you update your config file.

- Do_direct_payment()
- Get_balance()

The Adaptive Payments controller has one sample ready for demo.

- Convert_currency()

The PayFlow controller has one sample ready for demo.

- Process_transaction()

******
Notice
******

Our `standard class library <https://github.com/angelleye/paypal-php-library>`_ is now compatible with Composer which allows you to autoload the library and make it available in CodeIgniter.

We will still maintain this library for those of you who are not using Composer, but if you are you should probably look at the other library.


*********
Resources
*********

-  `PayPal Name-Value Pair API Developer Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_NVPAPI_DeveloperGuide.pdf>`_
-  `Adaptive Accounts Developer Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_AdaptiveAccounts.pdf>`_
-  `Adaptive Payments Developer Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_AdaptivePayments.pdf>`_
-  `Express Checkout Integration Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_ExpressCheckout_IntegrationGuide.pdf>`_
-  `Invoice Service API Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_InvoicingAPIGuide.pdf>`_
-  `Mass Payments User Guide <https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_MassPayment_Guide.pdf>`_
-  `PayPal Merchant Setup and Administration Guide <https://www.x.com/developers/paypal/development-and-integration-guides#msa>`_
-  `PayPal Payments Pro Documentation <https://www.x.com/developers/paypal/development-and-integration-guides#wpp>`_
-  `PayPal Recurring Billing / Recurring Payments Guide <https://www.x.com/developers/paypal/development-and-integration-guides#recurring>`_
