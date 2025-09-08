<?php

namespace Services\PaymentGateway;

class Razorpay
{

    CONST GATEWAY_NAME = 'Razorpay_Checkout';

    private $transaction_data;

    private $gateway;

    private $extra_params = ['razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature'];

    public function __construct($gateway, $paymentGatewayConfig)
    {
        $this->gateway = $gateway;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
        $this->options = [];
    }

    private function createTransactionData($order_total, $order_email, $event)
    {
        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => $event->currency->code,
            'description' => 'Order for customer: ' . $order_email,
            'receipt_email' => $order_email
        ];

        return $this->transaction_data;
    }

    public function startTransaction($order_total, $order_email, $event)
    {
        $this->createTransactionData($order_total, $order_email, $event);
        
        // Create Razorpay order first
        $orderData = [
            'amount' => $order_total * 100, // Razorpay expects amount in paise
            'currency' => $event->currency->code,
            'receipt' => 'order_rcptid_' . time(),
            'notes' => [
                'event_id' => $event->id,
                'customer_email' => $order_email
            ]
        ];
        
        $response = $this->gateway->purchase($orderData)->send();
        
        return $response;
    }

    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request)
    {
        foreach ($this->extra_params as $param) {
            if (!empty($request->get($param))) {
                $this->options[$param] = $request->get($param);
            }
        }
    }

    public function completeTransaction($data)
    {
        // Verify payment signature and capture payment
        $verifyData = [
            'razorpay_payment_id' => $this->options['razorpay_payment_id'],
            'razorpay_order_id' => $this->options['razorpay_order_id'],
            'razorpay_signature' => $this->options['razorpay_signature']
        ];
        
        $response = $this->gateway->completePurchase($verifyData)->send();
        
        return $response;
    }

    public function getAdditionalData($response = null)
    {
        $additionalData = [];
        if (isset($this->options['razorpay_payment_id'])) {
            $additionalData['razorpay_payment_id'] = $this->options['razorpay_payment_id'];
        }
        if (isset($this->options['razorpay_order_id'])) {
            $additionalData['razorpay_order_id'] = $this->options['razorpay_order_id'];
        }
        return $additionalData;
    }

    public function storeAdditionalData()
    {
        return true;
    }

    public function refundTransaction($order, $refund_amount, $refund_application_fee = null)
    {
        $request = $this->gateway->refund([
            'transactionReference' => $order->transaction_id,
            'amount' => $refund_amount * 100, // Convert to paise
            'notes' => [
                'reason' => 'Requested by customer',
                'order_id' => $order->id
            ]
        ]);

        $response = $request->send();

        if ($response->isSuccessful()) {
            $refundResponse['successful'] = true;
        } else {
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $response->getMessage();
        }

        return $refundResponse;
    }
}
