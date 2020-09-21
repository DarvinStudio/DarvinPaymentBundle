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
use Darvin\PaymentBundle\Workflow\Transitions;

/**
 * Renderer for void form
 */
class VoidFormRenderer extends AbstractFormRenderer
{
    /**
     * @inheritDoc
     */
    public function renderForm(Payment $payment): string
    {
        if (PaymentStateType::AUTHORIZED !== $payment->getState()) {
            throw new \LogicException('Wrong payment type');
        }

        $url = $this->urlBuilder->getVoidUrl($payment);

        return $this->twig->render('@DarvinPayment/admin/widget/operation_form.html.twig',[
            'url'       => $url,
            'id'        => $payment->getId(),
            'operation' => Transitions::VOID,
        ]);
    }
}
