<?php

namespace Corals\Modules\Payment\Fac\Message;

use Corals\Modules\Payment\Common\Exception\InvalidResponseException;
use Corals\Modules\Payment\Common\Message\RequestInterface;
use Corals\Modules\Payment\Fac\Message\AbstractResponse;

/**
 * FACPG2 XML Authorize Response
 */
class Authorize3DSResponse extends AbstractResponse
{
    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param string $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        if (empty($data)) {
            throw new InvalidResponseException();
        }

        $this->request = $request;
        $this->data = $this->xmlDeserialize($data);

        $this->verifySignature();
    }

    /**
     * Verifies the signature for the response.
     *
     * @throws InvalidResponseException if the signature doesn't match
     *
     * @return void
     */
    public function verifySignature()
    {
        if (isset($this->data['CreditCardTransactionResults']['ResponseCode']) && (
                '1' == $this->data['CreditCardTransactionResults']['ResponseCode'] ||
                '2' == $this->data['CreditCardTransactionResults']['ResponseCode'])) {
            $signature = $this->request->getMerchantPassword();
            $signature .= $this->request->getMerchantId();
            $signature .= $this->request->getAcquirerId();
            $signature .= $this->request->getTransactionId();

            $signature = base64_encode(sha1($signature, true));

            if ($signature !== $this->data['Signature']) {
                throw new InvalidResponseException('Signature verification failed');
            }
        }
    }

    /**
     * Return whether or not the response was successful
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return isset($this->data['ResponseCode']) && '0' === $this->data['ResponseCode'];
    }

    /**
     * Return the response's reason code
     *
     * @return string
     */
    public function getCode()
    {
        return isset($this->data['ReasonCode']) ? $this->data['ReasonCode'] : null;
    }

    /**
     * Return the response's reason message
     *
     * @return string
     */
    public function getMessage()
    {
        return isset($this->data['ReasonCodeDescription']) ? $this->data['ReasonCodeDescription'] : null;
    }


    /**
     * return the form needed for 3DS
     *
     * @return string|null
     */
    public function get3DSFormData()
    {

        return $this->data['HTMLFormData'] ?? null;
    }

}
