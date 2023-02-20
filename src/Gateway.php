<?php

namespace Corals\Modules\Payment\Fac;

use Corals\Modules\Payment\Common\AbstractGateway;
use Corals\User\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Str;
use LVR\CreditCard\CardCvc;
use LVR\CreditCard\CardExpirationMonth;
use LVR\CreditCard\CardExpirationYear;
use LVR\CreditCard\CardNumber;

/**
 * First Atlantic Commerce Payment Gateway 2 (XML POST Service)
 */
class Gateway extends AbstractGateway
{
    use ParameterTrait;
    use ValidatesRequests;

    /**
     * @return string Gateway name.
     */
    public function getName()
    {
        return 'Fac';
    }

    /**
     * @return array Default parameters.
     */
    public function getDefaultParameters()
    {
        return [
            'merchantId' => null,
            'merchantPassword' => null,
            'acquirerId' => \Settings::get('payment_fac_acquirerid', '464748'),
            'testMode' => false,
            'requireAvsCheck' => true
        ];
    }


    public function setAuthentication()
    {
        $merchant_id = '';
        $merchant_password = '';


        $sandbox = \Settings::get('payment_fac_sandbox_mode', 'true');

        if ($sandbox == 'true') {
            $this->setTestMode(true);
            $merchant_id = \Settings::get('payment_fac_sandbox_merchant_id');
            $merchant_password = \Settings::get('payment_fac_sandbox_merchant_password');
        } elseif ($sandbox == 'false') {
            $this->setTestMode(false);
            $merchant_id = \Settings::get('payment_fac_live_merchant_id');
            $merchant_password = \Settings::get('payment_fac_live_merchant_password');
        }
        $three_des = \Settings::get('payment_fac_three_des', false);

        $this->setMerchantId($merchant_id);
        $this->setMerchantPassword($merchant_password);
        $this->setThreeDes($three_des);
    }


    public function getPaymentViewName($type = null)
    {
        return "Fac::ecommerce";
    }

    /**
     * Authorize an amount on the customer’s card.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\AuthorizeRequest
     */
    public function authorize(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Capture an amount you have previously authorized.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\CaptureRequest
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\CaptureRequest', $parameters);
    }

    /**
     *  Authorize and immediately capture an amount on the customer’s card.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\PurchaseRequest', $parameters);
    }

    public function createCharge(array $parameters = array())
    {
        $this->setAuthentication();
        $this->setRequireAvsCheck(false);

        return $this->purchase($parameters);
    }

    /**
     *  Authorize and immediately capture an amount on the customer’s card using 3ds.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\Purchase3DSRequest
     */
    public function purchase3DS(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\Purchase3DSRequest', $parameters);
    }

    /**
     *  Refund an already processed transaction.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\RefundRequest
     */
    public function refund(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\RefundRequest', $parameters);
    }

    /**
     *  Reverse an already submitted transaction that hasn't been settled.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\VoidRequest
     */
    public function void(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\VoidRequest', $parameters);
    }

    /**
     *  Retrieve the status of any previous transaction.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\StatusRequest
     */
    public function status(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\StatusRequest', $parameters);
    }

    /**
     *  Create a stored card and return the reference token for future transactions.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\CreateCardRequest
     */
    public function createCard(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\CreateCardRequest', $parameters);
    }

    /**
     *  Update a stored card.
     *
     * @param array $parameters
     *
     * @return \Corals\Modules\Payment\Fac\Message\UpdateCardRequest
     */
    public function updateCard(array $parameters = [])
    {
        return $this->createRequest('\Corals\Modules\Payment\Fac\Message\UpdateCardRequest', $parameters);
    }

    public function prepareCreateChargeParameters($order, User $user, $checkoutDetails)
    {
        return [
            'amount' => $order->amount,
            'currency' => $order->currency,
            'transactionId' => Str::random() . '-' . $order->order_number,
            'card' => $checkoutDetails['payment_details']
        ];
    }

    public function prepareCreateMultiOrderChargeParameters($orders, User $user, $checkoutDetails)
    {
        $amount = 0;
        $description = "Order # ";
        $currency = "";
        foreach ($orders as $order) {
            $amount += $order->amount;
            $currency = $order->currency;
            $description .= $order->order_number . ",";
        }

        return [
            'amount' => $amount,
            'currency' => $currency,
            'transactionId' => Str::random(),
            'card' => $checkoutDetails['payment_details']
        ];
    }

    public function validateRequest($request)
    {
        return $this->validate($request, [
            'payment_details.number' => ['required', new CardNumber()],
            'payment_details.expiryYear' => [
                'required',
                new CardExpirationYear($request->input('payment_details.expiryMonth', ''))
            ],
            'payment_details.expiryMonth' => [
                'required',
                new CardExpirationMonth($request->input('payment_details.expiryYear', ''))
            ],
            'payment_details.cvv' => [
                'required',
                new CardCvc($request->input('payment_details.number', ''))
            ],
        ], [], [
            'payment_details.number' => trans('Fac::attributes.card_number'),
            'payment_details.expiryYear' => trans('Fac::attributes.expYear'),
            'payment_details.expiryMonth' => trans('Fac::attributes.expMonth'),
            'payment_details.cvv' => trans('Fac::attributes.cvv'),
        ]);
    }

    public function loadScripts()
    {
        return view("Fac::scripts")->render();
    }
}
