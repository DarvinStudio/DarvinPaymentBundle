<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Twig\Extension\Url;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * URL builder Twig extension
 */
class UrlBuilderExtension extends AbstractExtension
{
    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var string[]
     */
    private $gatewayNames;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder Payment URL builder
     * @param array                                                $bridges    Bridges config
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder, array $bridges)
    {
        $this->urlBuilder = $urlBuilder;

        $this->gatewayNames = array_keys(array_filter($bridges, function (array $bridge): bool {
            return $bridge['enabled'];
        }));
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('payment_purchase_urls', [$this, 'getPurchaseUrls']),
        ];
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return array
     */
    public function getPurchaseUrls(Payment $payment): array
    {
        $urls = [];

        foreach ($this->gatewayNames as $gatewayName) {
            $urls[$gatewayName] = $this->urlBuilder->getPurchaseUrl($payment, $gatewayName);
        }

        return $urls;
    }
}
