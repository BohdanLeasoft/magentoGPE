<?php

namespace GingerPay\Payment\Tests\Mocks;

class Customer
{
    public $websiteId;
    public $store;
    public $firstName;
    public $lastName;
    public $email;
    public $password;

    public function setWebsiteId($id)
    {
        $this->websiteId = $id;
    }

    public function setStore($store)
    {
        $this->store = $store;
    }

    public function setFirstname($firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastname($lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getId()
    {
        return 42;
    }

    public function get($order, $method)
    {
        return self::getCustomerData();
    }

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

    public function getEmail()
    {
        return 'Test3@ukr.net';
    }

    public function getFirstname()
    {
        return 'Jon';
    }

    public function create()
    {
        return $this;
    }

    public function loadByEmail($email)
    {
        return $this;
    }

    public function getById($id)
    {
        return $this;
    }

    public function getLastname()
    {
        return 'Lastname';
    }

    public function getPrefix()
    {
        return 'Prefix';
    }

    public function getSuffix()
    {
        return 'Suffix';
    }

    public function getStreet()
    {
        return 'Street';
    }

    public function getCity()
    {
        return 'City';
    }

    public function getCountryId()
    {
        return 'CountryId';
    }

    public function getRegion()
    {
        return 'Region';
    }

    public function getRegionId()
    {
        return 'RegionId';
    }
    public function getPostcode()
    {
        return 'Postcode';
    }
    public function getTelephone()
    {
        return '0505869999';
    }
    public function getFax()
    {
        return 'Fax';
    }

}
