<?php

namespace GingerPay\Payment\Tests\Mocks;

class Customer
{
    private static function getPhoneNumber()
    {
        return ['0' => '0555869119'];
    }

    public static function getCustomerData()
    {
        return [
            'merchant_customer_id' => '638',
            'email_address' => 'Test3@ukr.net',
            'first_name' => 'Jon',
            'last_name' => 'Doe',
            'address_type' => 'billing',
            'address' => 'Donauweg 10',
            'postal_code' => '1043 AJ',
            'housenumber' => '10',
            'country' => 'NL',
            'phone_numbers' => self::getPhoneNumber()
        ];
    }
}
