# DarvinPaymentBundle
This Omnipay-based bundle provides basic payment logic for Symfony applications.

## Features:

- creating payment
- approving payment before purchase
- standard purchase and purchase with pre-authorise
- refund or canceling payment
- the sending notify emails of every changed payment state
- logging every actions in log and Event entity
- provides the interface for the adding receipt to payment

## Payment states

To manage payment state used Symfony Workflow Component.

Scheme of payment state changes:

![Scheme of payment state changes](Resources/doc/workflow.png)

## Installation
```bash
    composer require darvinstudio/darvin-payment-bundle
```

## How to create Payment
```php
    /** @var $paymentFactory \Darvin\PaymentBundle\Payment\Factory\PaymentFactoryInterface */
    $payment = $paymentFactory->createPayment(
        new PaidOrder(
            (string)$order->getId(),
            get_class($order),
            (string)$order->getNumber()
        ),
        $order->getPrice(),
        new Client(
            (string)$user->getId(),
            get_class($user),
            $user->getEmail()
        ),
        'USD'
    );
```

## How to get payment's link in twig
```twig
    {{ payment_purchase_urls(payment) }}
```

## How to get all available payment's links for order
```twig
    {{ payment_purchase_widget(order) }}
```
