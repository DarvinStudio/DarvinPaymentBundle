<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Admin\View\Widget;

use Darvin\AdminBundle\Security\Permissions\Permission;
use Darvin\AdminBundle\View\Widget\Widget\AbstractWidget;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface;
use Darvin\PaymentBundle\Payment\Operations;

/**
 * Operation admin view widget
 */
class PaymentOperationWidget extends AbstractWidget
{
    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface
     */
    private $formRenderer;

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface $formRenderer Operation admin form renderer
     */
    public function __construct(OperationFormRendererInterface $formRenderer)
    {
        $this->formRenderer = $formRenderer;
    }

    /**
     * {@inheritDoc}
     */
    protected function createContent($entity, array $options): ?string
    {
        /** @var \Darvin\PaymentBundle\Entity\Payment $payment */
        $payment = $entity;
        $forms   = [];

        foreach (array_keys(Operations::OPERATIONS) as $operation) {
            if ($this->formRenderer->canRenderForm($payment, $operation)) {
                $forms[] = $this->formRenderer->renderForm($payment, $operation);
            }
        }
        if (empty($forms)) {
            return null;
        }

        return implode('', $forms);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAllowedEntityClasses(): iterable
    {
        yield Payment::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredPermissions(): iterable
    {
        yield Permission::EDIT;
    }
}
