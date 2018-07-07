<?php
namespace Darvin\PaymentBundle\Gateway\Telr;

use Darvin\PaymentBundle\Gateway\Telr\Action\AuthorizeAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\CancelAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\ConvertPaymentAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\CaptureAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\NotifyAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\RefundAction;
use Darvin\PaymentBundle\Gateway\Telr\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class TelrGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'telr',
            'payum.factory_title' => 'telr',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api((array) $config, $config['payum.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
