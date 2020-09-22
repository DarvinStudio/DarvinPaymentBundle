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
use Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer;
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Operation view widget
 */
class PaymentOperationWidget extends AbstractWidget
{
    public const ALIAS = 'payment_operation';

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
     * @param \Symfony\Component\Workflow\WorkflowInterface                $workflow            Workflow for payment state
     * @param \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer|null $approveFormRenderer Approve form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer|null $captureFormRenderer Capture form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer|null  $refundFormRenderer  Refund form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer|null    $voidFormRenderer    Void form renderer
     */
    public function __construct(
        WorkflowInterface $workflow,
        ?ApproveFormRenderer $approveFormRenderer,
        ?CaptureFormRenderer $captureFormRenderer,
        ?RefundFormRenderer $refundFormRenderer,
        ?VoidFormRenderer $voidFormRenderer
    ) {
        $this->workflow = $workflow;
        $this->approveFormRenderer = $approveFormRenderer;
        $this->captureFormRenderer = $captureFormRenderer;
        $this->refundFormRenderer = $refundFormRenderer;
        $this->voidFormRenderer = $voidFormRenderer;
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias(): string
    {
        return self::ALIAS;
    }

    /**
     * {@inheritDoc}
     */
    protected function createContent($entity, array $options): ?string
    {
        $content = '';

        if ($this->approveFormRenderer !== null && $this->workflow->can($entity, Transitions::APPROVE)) {
            $content .= $this->approveFormRenderer->renderForm($entity);
        }

        if ($this->captureFormRenderer !== null && $this->workflow->can($entity, Transitions::CAPTURE)) {
            $content .= $this->captureFormRenderer->renderForm($entity);
        }

        if ($this->voidFormRenderer !== null && $this->workflow->can($entity, Transitions::VOID)) {
            $content .= $this->voidFormRenderer->renderForm($entity);
        }

        if ($this->refundFormRenderer !== null && $this->workflow->can($entity, Transitions::REFUND)) {
            $content .= $this->refundFormRenderer->renderForm($entity);
        }

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    protected function getAllowedEntityClasses(): iterable
    {
        yield \Darvin\PaymentBundle\Entity\Payment::class;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredPermissions(): iterable
    {
        yield Permission::EDIT;
    }
}
