<?php
namespace Darvin\PaymentBundle\Gateway\Telr;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     * @param MessageFactory      $messageFactory
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param       $method
     * @param       $uri
     * @param array $data
     *
     * @return array
     */
    protected function doRequest($method, $uri, array $data)
    {
        $headers = [];

        $request = $this->messageFactory->createRequest($method, $uri, $headers, http_build_query($data));

        $response = $this->client->send($request);

        if (false == ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        $result = json_decode((string)$response->getBody(), true);

        if (isset($result['error'])) {
            throw new \RuntimeException(sprintf(
                "%s: %s",
                $result['error']['message'],
                $result['error']['note']
            ));
        }
        return $result;
    }

    public function createRequest(array $data)
    {
        $data = array_merge([
            'ivp_method'  => 'create',
            'ivp_store'   => '20215',
            'ivp_authkey' => 'hfKhb@BB2t~NTL94',
        ], $data);

        return $this->doRequest('POST', 'https://secure.telr.com/gateway/order.json', $data);
    }

    /**
     * @return string
     */
    protected function getApiEndpoint()
    {
        return $this->options['sandbox'] ? 'http://sandbox.example.com' : 'http://example.com';
    }
}
