<?php
namespace Darvin\PaymentBundle\Gateway\Telr\Action;

use Darvin\PaymentBundle\Gateway\Telr\Api;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Exception\RequestNotSupportedException;

class CaptureAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var Api
     */
    protected $api;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $return = $request->getToken()->getAfterUrl();

        $data = array_merge(ArrayObject::ensureArrayObject($request->getModel())->getArrayCopy(), [
            'return_auth' => $return,
            'return_decl' => $return,
            'return_can'  => $return,
            'ivp_test' => 1
        ]);

        $result = $this->api->createRequest($data);


        $request->getModel()['ref'] = $result['order']['ref'];
        $request->getToken()->setAfterUrl($result['order']['url']);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess &&
            $request->getFirstModel() instanceof PaymentInterface
        ;
    }

    /**
     * @param mixed $api
     *
     * @throws UnsupportedApiException if the given Api is not supported.
     */
    public function setApi($api)
    {
        if (!$api instanceof Api) {
            throw new UnsupportedApiException();
        }

        $this->api = $api;
    }
}
