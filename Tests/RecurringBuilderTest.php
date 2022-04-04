<?php

namespace GingerPay\Payment\Tests;

require_once __DIR__.'/ClassSeparators/RecurringBuilderSeparator.php';
require_once  __DIR__.'/ClassSeparators/ConfigRepositoryBuilderSeparator.php';

use GingerPay\Payment\Tests\ClassSeparators\RecurringBuilderSeparator;
use GingerPay\Payment\Tests\Mocks\Order;
use PHPUnit\Framework\TestCase;
use GingerPay\Payment\Tests\ClassSeparators\CreditcardSeparator as Creditcard;
use GingerPay\Payment\Tests\ClassSeparators\ConfigRepositoryBuilderSeparator;

class RecurringBuilderTest extends TestCase
{
    private $recurringBuilder;
    private $order;
    private $configRepository;
    private $expectedArray;

    public function setUp() : void    {
        $this->order = new Order();
        $this->recurringBuilder = new RecurringBuilderSeparator();
        $this->configRepository = new ConfigRepositoryBuilderSeparator();

        $this->expectedArray = array(
            "currency" => "EUR",
            "amount" => 500,
            "merchant_order_id" => 638,
            "customer"  => [
                "merchant_customer_id" => "638",
                "email_address" => "Test3@ukr.net",
                'first_name' => "Jon",
                'last_name' => "Doe",
                'address_type' => "billing",
                'address' => "Donauweg 10",
                'postal_code' => "1043 AJ",
                'housenumber' => "10",
                'country' => "NL",
                'phone_numbers' => [ '0' => "0555869119"]],
            "description" => "Your order 638 at Your order %id% at %name%",
            "return_url" => "https://magento2.test/ginger/checkout/process/",

            "transactions" => [[
                "payment_method" => 'credit-card',
                "payment_method_details" => [
                    "vault_token" => 'c4da3cdb-aa96-48cc-ba09-22a321c801e6',
                    "recurring_type" => 'recurring'
                ]
            ]],
            "extra" => [
                "user_agent" => 'USER_AGENT',
                "platform_name" => "Magento2",
                "platform_version" => '2.2.11',
                "plugin_name" => $this->configRepository->getPluginName(),
                "plugin_version" => $this->configRepository->getPluginVersion()],
            "order_lines" => [[
                '0' => [
                    'type' => 'physical',
                    'url' => 'https://magento2.test/newsuperproduct.html',
                    'name' => 'NewSuperProduct',
                    'amount' => '500',
                    'currency' => 'EUR',
                    'quantity' => '1',
                    'vat_percentage' => 0,
                    'merchant_order_line_id' => 638
                ]
            ]],
            "webhook_url" => "https://magento2.test/ginger/checkout/webhook/"
        );

    }

    public function testIsOrderForRecurring()
    {
        $this->assertTrue($this->recurringBuilder->isOrderForRecurring($this->order), 'It should be true. False returned');
    }

    public function testCancelRecurringOrderSuccess()
    {
        $this->assertEquals('success', $this->recurringBuilder->cancelRecurringOrder($this->order->getGingerpayTransactionId()), 'Unexpected result');
    }

    public function testCancelRecurringOrderFalse()
    {
        $this->assertFalse($this->recurringBuilder->cancelRecurringOrder(null), 'Unexpected result');
    }

    public function testGetAddressArray()
    {
        $expectedResult = [
            'firstname' => "Jon",
            'lastname' => "Lastname",
            'prefix' => "Prefix",
            'suffix' => "Suffix",
            'street' => "Street",
            'city' => "City",
            'country_id' => "CountryId",
            'region' => "Region",
            'region_id' => "RegionId",
            'postcode' => "Postcode",
            'telephone' => "0505869999",
            'fax' => "Fax",
            'save_in_address_book' => 1
        ];

        $this->assertEquals($expectedResult, $this->recurringBuilder->helperDataBuilder->getAddressArray($this->order->getBillingAddress()), 'Unexpected array is given');
    }

    public function testCreateOrder()
    {
        $this->assertEquals($this->order, $this->recurringBuilder->createOrder($this->order), 'Wrong order object returned');
    }

    public function testPrepareGingerOrder()
    {
        $this->assertEquals($this->expectedArray, $this->recurringBuilder->prepareGingerOrder($this->order), '');

    }

}
