Magento 2 2.0.0
------------------------------   

# Changelog #

## Changes in version 1.0.0
+ Initial version
## Changes in version 1.0.1
+ Webhook payload with unknown transaction id will not create error log
+ Expired orders will not trigger order emails
+ Cancellation message in cart is now correctly translated

## Changes in version 1.0.2
+ Added error message to admin when refunds are not possible
+ Populate merchant_order_id with IncrementId instead of EntityId
+ Date of birth and prefix are now added to Klarna request
+ Klarna Pay Later redirection

## Changes in version 1.0.3

* Fixes checkout success issue with non EMS method
* Added extra check and loggin on paid invoice
* Updated minimum requirements to Magento 2.2.x & PHP 7.x

## Changes in version 1.0.4

* Added iDEAL issuer validation

## Changes in version 1.0.5

* iDEAL issuer default value translation

## Changes in version 2.0.0

* Refactored code to handle GPE solution.
* Unified bank labels to handle GPE solution.
* Added the Google-pay, Sofort, Klarna DD payment methods.
* Added Multi-Currency solution.
* Added ApplePay detection.
* Added function which adaptively returns a message using 'customer_message'.
* Added test which check is project structure correct using PHPUnit extension to check GPE solution on step GitHub actions.
* Added test which check order creating using PHPUnit extension to check that latest changes doesn't crash the main functionality.

