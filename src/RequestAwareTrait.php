<?php

namespace TrustPay;

trait RequestAwareTrait
{
    /** @var SignatureValidatorInterface */
    protected $signatureValidator;

    /** @var integer */
    protected $accountId;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $amount;

    /** @var string */
    protected $reference;

    /** @var string */
    protected $currency;

    /** @var string */
    protected $language;

    /** @var string */
    protected $country;

    /** @var string */
    protected $description;

    /** @var string */
    protected $customerEmail;

    /** @var BillingAddress */
    protected ?BillingAddress $billingAddress = null;

    /** @var string */
    protected $returnUrl;

    /** @var string */
    protected $successUrl;

    /** @var string */
    protected $cancelUrl;

    /** @var string */
    protected $errorUrl;

    /** @var string */
    protected $notificationUrl;

    /**
     * @var int
     */
    protected $paymentType;

    /**
     * @param SignatureValidatorInterface $signatureValidator
     */
    public function setSignatureValidator(SignatureValidatorInterface $signatureValidator)
    {
        $this->signatureValidator = $signatureValidator;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }


    /**
     * @param string $amount
     */
    public function setAmount($amount)
    {
        $this->amount = number_format($amount, 2);
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $customerEmail
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;
    }

    /**
     * @param string $billingAddress
     */
    public function setBillingAddress(BillingAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @param string $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @param string $successUrl
     */
    public function setSuccessUrl($successUrl)
    {
        $this->successUrl = $successUrl;
    }

    /**
     * @param string $cancelUrl
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * @param string $errorUrl
     */
    public function setErrorUrl($errorUrl)
    {
        $this->errorUrl = $errorUrl;
    }

    /**
     * @param string $notificationUrl
     */
    public function setNotificationUrl($notificationUrl)
    {
        $this->notificationUrl = $notificationUrl;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return array
     */
    public function getDefaultQueryData()
    {
        $queryData = [
            'AccountId'  => $this->accountId,
            'Amount'  => $this->amount,
            'Currency'  => $this->currency,
            'Reference'  => $this->reference,
            'Url'  => $this->returnUrl,
            'ReturnUrl' => $this->successUrl,
            'CancelUrl' => $this->cancelUrl,
            'ErrorUrl' => $this->errorUrl,
            'NotificationUrl' => $this->notificationUrl,
            'Signature'  => $this->createStandardSignature(),
            'Localization'  => $this->language,
            'Country'  => $this->billingAddress->getBillingCountry(),
            'Description'  => $this->description,
            'Email'  => $this->customerEmail,
            //
            'PaymentType' => $this->paymentType,

        ];

        if ($this->billingAddress) {
            $queryData = array_merge($queryData, [
                'CardHolder' => $this->billingAddress->getCardHolder(),
                'BillingStreet' => $this->billingAddress->getBillingStreet(),
                'BillingPostcode' => $this->billingAddress->getBillingPostcode(),
                'BillingCity' => $this->billingAddress->getBillingCity(),
                'BillingCountry' => $this->billingAddress->getBillingCountry(),
            ]);
        }

        $queryData = array_filter($queryData, function ($value) {
            return $value !== null;
        });

        return $queryData;
    }

    /**
     * @throws Exceptions\RequestMissingRequiredField
     * @return string
     */
    public function getUrl()
    {
        foreach ([ 'amount', 'reference', 'currency', 'paymentType' ] as $required) {
            if ($this->$required === null) {
                throw new Exceptions\RequestMissingRequiredField("The {$required} field is required.");
            }
        }

        return $this->endpoint . '?' . $this->buildQuery();
    }

    /**
     * @param $result
     *
     * @return Response
     */
    protected function parseBackgroundResponse($result)
    {
        $responseData = [
            'REF' => $this->reference,
        ];

        if ($result['isOk'] === true) {
            $response = [];
            parse_str($result['response'], $response); // TrustPay return this data as "result=RES"
            $responseData['RES'] = $response['result'];
        } else {
            $responseData['RES'] = 1001;
        }

        return new Response($responseData);
    }

    protected function createStandardSignature()
    {
        $message = $this->signatureValidator->createMessage(
            $this->accountId,
            $this->amount,
            $this->currency,
            $this->reference,
            $this->paymentType,
            $this->billingAddress->getBillingCity(),
            $this->billingAddress->getBillingCountry(),
            $this->billingAddress->getBillingPostcode(),
            $this->billingAddress->getBillingStreet(),
            $this->billingAddress->getCardHolder(),
            $this->customerEmail,
        );

        return $this->signatureValidator->computeSignature($message);
    }

    /**
     * @return mixed
     */
    abstract protected function buildQuery();
}
