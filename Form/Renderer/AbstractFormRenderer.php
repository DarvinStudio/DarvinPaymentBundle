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

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Twig\Environment;

/**
 * Renderer for form of admin operation
 */
abstract class AbstractFormRenderer
{
    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

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
     * @return string
     */
    abstract public function renderForm(Payment $payment): string;
}
