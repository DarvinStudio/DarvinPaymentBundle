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

/**
 * Operation admin form renderer
 */
interface OperationFormRendererInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment   Payment
     * @param string                               $operation Operation
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderForm(Payment $payment, string $operation): string;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment   Payment
     * @param string                               $operation Operation
     *
     * @return bool
     */
    public function canRenderForm(Payment $payment, string $operation): bool;
}
