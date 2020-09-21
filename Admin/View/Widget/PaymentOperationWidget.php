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
use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer;
use Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer;

/**
 * Operation view widget
 */
class PaymentOperationWidget extends AbstractWidget
{
    public const ALIAS = 'payment_operation';

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer
     */
    private $approveFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer
     */
    private $captureFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer
     */
    private $refundFormRenderer;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer
     */
    private $voidFormRenderer;

    /**
     * @param \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer $approveFormRenderer Approve form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer $captureFormRenderer Capture form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\RefundFormRenderer  $refundFormRenderer  Refund form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\VoidFormRenderer    $voidFormRenderer    Void form renderer
     */
    public function __construct(
        ApproveFormRenderer $approveFormRenderer,
        CaptureFormRenderer $captureFormRenderer,
        RefundFormRenderer $refundFormRenderer,
        VoidFormRenderer $voidFormRenderer
    ) {
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
        if (PaymentStateType::APPROVAL === $entity->getState()) {
            return $this->approveFormRenderer->renderForm($entity);
        }

        if (PaymentStateType::AUTHORIZED === $entity->getState()) {
            return $this->captureFormRenderer->renderForm($entity);
        }

        if (PaymentStateType::COMPLETED === $entity->getState()) {
            return $this->refundFormRenderer->renderForm($entity);
        }

        if (PaymentStateType::AUTHORIZED === $entity->getState()) {
            return $this->voidFormRenderer->renderForm($entity);
        }

        return null;
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
