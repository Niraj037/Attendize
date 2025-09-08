<?php

namespace Services\PaymentGateway;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\ResponseInterface;

class Razorpay
{
    public const GATEWAY_NAME = 'Razorpay_Checkout';

    private array $transaction_data = [];

    private AbstractGateway $gateway;

    private array $paymentGatewayConfig;

    private array $options = [];

    private array $extra_params = ['razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature'];

    public function __construct(AbstractGateway $gateway, array $paymentGatewayConfig)
    {
        $this->gateway = $gateway;
        $this->paymentGatewayConfig = $paymentGatewayConfig;
    }

    private function createTransactionData(float $order_total, string $order_email, $event): array
    {
        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => $event->currency->code,
            'description' => 'Order for customer: ' . $order_email,
            'receipt_email' => $order_email
        ];

        return $this->transaction_data;
    }

    public function startTransaction(float $order_total, string $order_email, $event): ResponseInterface
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

    public function getTransactionData(): array
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request): void
    {
        foreach ($this->extra_params as $param) {
            if (!empty($request->get($param))) {
                $this->options[$param] = $request->get($param);
            }
        }
    }

    public function completeTransaction(array $data): ResponseInterface
    {
        // Verify payment signature and capture payment
        $verifyData = [
            'razorpay_payment_id' => $this->options['razorpay_payment_id'] ?? null,
            'razorpay_order_id' => $this->options['razorpay_order_id'] ?? null,
            'razorpay_signature' => $this->options['razorpay_signature'] ?? null
        ];
        
        $response = $this->gateway->completePurchase($verifyData)->send();
        
        return $response;
    }

    public function getAdditionalData(?ResponseInterface $response = null): array
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

    public function storeAdditionalData(): bool
    {
        return true;
    }

    public function refundTransaction($order, float $refund_amount, ?float $refund_application_fee = null): array
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

        $refundResponse = [];
        if ($response->isSuccessful()) {
            $refundResponse['successful'] = true;
        } else {
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $response->getMessage();
        }

        return $refundResponse;
    }
}
