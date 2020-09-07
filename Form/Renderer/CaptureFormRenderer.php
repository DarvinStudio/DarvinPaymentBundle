<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Form\Renderer;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\Exception\ActionNotImplementedException;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Twig\Environment;

/**
 * Renderer for capture form
 */
class CaptureFormRenderer
{
    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder Url builder
     * @param \Twig\Environment                                    $twig       Twig
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder, Environment $twig)
    {
        $this->urlBuilder = $urlBuilder;
        $this->twig = $twig;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return string|null
     */
    public function renderForm(Payment $payment): ?string
    {
        if (PaymentStateType::AUTHORIZED !== $payment->getState() || null === $payment->getGatewayName()) {
            return null;
        }

        try {
            $url = $this->urlBuilder->getCaptureUrl($payment);
        } catch (ActionNotImplementedException $ex) {
            return sprintf('<p>%s</p>', $ex->getMessage());
        }

        return $this->twig->render('@DarvinPayment/admin/widget/capture_form.html.twig',[
            'url' => $url,
            'id'  => $payment->getId(),
        ]);
    }
}