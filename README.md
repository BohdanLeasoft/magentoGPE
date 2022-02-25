# EMS Online plugin for Magento 2

## About
This is the offical EMS Online plugin.

EMS helps entrepreneurs with the best, smartest and most efficient payment systems. Both 
in your physical store and online in your webshop. With a wide range of payment methods 
you can serve every customer.

Why EMS?

Via the EMS website you can create a free test account online 24/7 and try out the online 
payment solution. EMS's online solution also offers the option of sending payment links and 
accepting QR payments.

The ideal online payment page for your webshop: 

·         Free test account - apply online 24/7 

·         Wide range of payment methods 

·         Payment page entirely in the style of your website, making transactions less likely to be terminated. 

·         Download your reports in the formats CAMT.053, MT940, MT940S & COD 

·         One clear dashboard for all your payment, revenue and administrative functions 

·         Available in 4 languages: English, French, Dutch, and German. More languages will be added. 


## Version number                 
 
* Latest version 2.0.0
 
## Requirements:       
- PHP v7.0 to v7.4
- Magento v2.2.x to v2.4.3
                              
## Supported methods ##
* Afterpay
* Amex
* Apple Pay
* Bancontact
* Banktransfer
* Creditcard
* iDEAL
* Klarna Pay Later
* Klarna Pay Now
* Klarna Direkt Debit
* Google-pay
* Sofort
* Payconiq
* Paypal
* Tikkie

## Installation using Composer ##
Magento® 2 uses the Composer to manage the module package and the library. Composer is a dependency manager for PHP. Composer declares the libraries your project depends on and it will manage (install/update) them for you.

Check if your server has composer installed by running the following command:
```
composer –v
``` 
If your server doesn’t have composer installed, you can easily install it by using this manual: https://getcomposer.org/doc/00-intro.md

Step-by-step to install the Magento® 2 extension through Composer:

1.	Connect to your server running Magento® 2 using SSH or another method (make sure you have access to the command line).
2.	Locate your Magento® 2 project root.
3.	Install the Magento® 2 extension through composer and wait till it's completed:
```
composer require emspay/ems-online-magento-2
``` 
4.	After that run the Magento® upgrade and clean the caches:
```
php bin/magento setup:upgrade
php bin/magento module:enable GingerPay_Payment
```
If you install version 1.0.5 and older run:
```
php bin/magento module:enable EMSPay_Payment
```
5.  If Magento® is running in production mode you also need to redeploy the static content:
```
php bin/magento setup:static-content:deploy
```
6.  After the installation: Go to your Magento® admin portal and open ‘Stores’ > ‘Configuration’ > ‘Payment Methods’ > ‘EMSPay’.


## Additional ways to install ##

### Manual instalation ###

1. Go to app/code folder 
2. Unzip ems-online.zip file wich attached to [release](https://github.com/emspay/ems-online-magento-2/releases) 
3. Continue installation from step 4 in "Installation using Composer"



