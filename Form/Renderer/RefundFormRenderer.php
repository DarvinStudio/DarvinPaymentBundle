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
use Darvin\PaymentBundle\Workflow\Transitions;

/**
 * Renderer for refund form
 */
class RefundFormRenderer extends AbstractFormRenderer
{
    /**
     * @inheritDoc
     */
    public function renderForm(Payment $payment): string
    {
        if (!$this->workflow->can($payment, Transitions::REFUND)) {
            throw new \LogicException('Wrong payment type');
        }

        return $this->twig->render('@DarvinPayment/admin/widget/operation_form.html.twig',[
            'url'       => $this->urlBuilder->getRefundUrl($payment),
            'id'        => $payment->getId(),
            'operation' => Transitions::REFUND,
        ]);
    }
}
