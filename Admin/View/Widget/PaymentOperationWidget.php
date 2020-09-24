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
use Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer;
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Operation admin view widget
 */
class PaymentOperationWidget extends AbstractWidget
{
    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    private $workflow;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer|null
     */
    private $approveFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer|null
     */
    private $captureFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer|null
     */
    private $refundFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer|null
     */
    private $voidFormRenderer;

    /**
     * @param \Symfony\Component\Workflow\WorkflowInterface $workflow Workflow for payment state
     */
    public function __construct(WorkflowInterface $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer|null $approveFormRenderer Approve form renderer
     */
    public function setApproveFormRenderer(?ApproveFormRenderer $approveFormRenderer): void
    {
        $this->approveFormRenderer = $approveFormRenderer;
    }

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer|null $captureFormRenderer Capture form renderer
     */
    public function setCaptureFormRenderer(?CaptureFormRenderer $captureFormRenderer): void
    {
        $this->captureFormRenderer = $captureFormRenderer;
    }

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer|null $refundFormRenderer Refund form renderer
     */
    public function setRefundFormRenderer(?RefundFormRenderer $refundFormRenderer): void
    {
        $this->refundFormRenderer = $refundFormRenderer;
    }

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer|null $voidFormRenderer Void form renderer
     */
    public function setVoidFormRenderer(?VoidFormRenderer $voidFormRenderer): void
    {
        $this->voidFormRenderer = $voidFormRenderer;
    }

    /**
     * {@inheritDoc}
     */
    protected function createContent($entity, array $options): ?string
    {
        $forms = [];

        if ($this->approveFormRenderer !== null && $this->workflow->can($entity, Transitions::APPROVE)) {
            $forms[] = $this->approveFormRenderer->renderForm($entity);
        }
        if ($this->captureFormRenderer !== null && $this->workflow->can($entity, Transitions::CAPTURE)) {
            $forms[] = $this->captureFormRenderer->renderForm($entity);
        }
        if ($this->voidFormRenderer !== null && $this->workflow->can($entity, Transitions::VOID)) {
            $forms[] = $this->voidFormRenderer->renderForm($entity);
        }
        if ($this->refundFormRenderer !== null && $this->workflow->can($entity, Transitions::REFUND)) {
            $forms[] = $this->refundFormRenderer->renderForm($entity);
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
