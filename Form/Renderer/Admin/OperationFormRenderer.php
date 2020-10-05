<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Form\Renderer\Admin;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Darvin\PaymentBundle\Payment\Operations;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * Operation admin form renderer
 */
class OperationFormRenderer implements OperationFormRendererInterface
{
    private const URL_GENERATORS = [
        Operations::APPROVE => 'getApproveUrl',
        Operations::CAPTURE => 'getCaptureUrl',
        Operations::REFUND  => 'getRefundUrl',
        Operations::VOID    => 'getVoidUrl',
    ];

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    private $workflow;

    /**
     * @param \Twig\Environment                                    $twig       Twig
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder Payment URL builder
     * @param \Symfony\Component\Workflow\WorkflowInterface        $workflow   Payment workflow
     */
    public function __construct(Environment $twig, PaymentUrlBuilderInterface $urlBuilder, WorkflowInterface $workflow)
    {
        $this->twig = $twig;
        $this->urlBuilder = $urlBuilder;
        $this->workflow = $workflow;
    }

    /**
     * {@inheritDoc}
     */
    public function renderForm(Payment $payment, string $operation): string
    {
        if (!$this->canRenderForm($payment, $operation)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" operation form cannot be rendered for payment "%s".', $operation, $payment->__toString())
            );
        }

        return $this->twig->render('@DarvinPayment/admin/operation/form.html.twig',[
            'url'       => $this->urlBuilder->{self::URL_GENERATORS[$operation]}($payment),
            'id'        => $payment->getId(),
            'operation' => $operation,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function canRenderForm(Payment $payment, string $operation): bool
    {
        return isset(self::URL_GENERATORS[$operation]) && $this->workflow->can($payment, $operation);
    }
}
