<?php

namespace TrustPay;

class BillingAddress {
    private $cardHolder;
    private $billingStreet;
    private $billingCity;
    private $billingCountry;
    private $billingPostcode;

    public function __construct(
        $cardHolder,
        $billingStreet,
        $billingCity,
        $billingPostcode,
        $billingCountry
    )
    {
        $this->cardHolder = $cardHolder;
        $this->billingStreet = $billingStreet;
        $this->billingCity = $billingCity;
        $this->billingPostcode = $billingPostcode;
        $this->billingCountry = $billingCountry;
    }

    public function getBillingStreet()
    {
        return $this->billingStreet;
    }

    public function getBillingCity()
    {
        return $this->billingCity;
    }

    public function getBillingPostcode()
    {
        return $this->billingPostcode;
    }

    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    public function getCardHolder()
    {
        return $this->cardHolder;
    }
}