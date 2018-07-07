<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:57
 */

namespace Darvin\PaymentBundle\UrlBuilder;


use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;
use Symfony\Component\Routing\RouterInterface;

class DefaultPaymentUrlBuilder implements PaymentUrlBuilderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * DefaultPaymentUrlBuilder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizationUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('authorization');
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('capture');
    }


    /**
     * @inheritDoc
     */
    public function getPurchaseUrl(PaymentInterface $payment, $gateway = null)
    {
        return $this->router->generate('darvin_payment_payment_purchase',
            [
                'id'      => $payment->getId(),
                'gateway' => $gateway,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getSuccessUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        // TODO: Implement getSuccessUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function getCanceledUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        // TODO: Implement getCanceledUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function getFailedUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        // TODO: Implement getFailedUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function getRefundUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('refund');
    }

    /**
     * @inheritDoc
     */
    public function getNotifyUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('notify');
    }
}