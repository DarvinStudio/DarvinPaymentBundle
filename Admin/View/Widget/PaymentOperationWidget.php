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
     * @param \Darvin\PaymentBundle\Form\Renderer\ApproveFormRenderer $approveFormRenderer Approve form renderer
     * @param \Darvin\PaymentBundle\Form\Renderer\CaptureFormRenderer $captureFormRenderer Capture form renderer
     */
    public function __construct(
        ApproveFormRenderer $approveFormRenderer,
        CaptureFormRenderer $captureFormRenderer
    ) {
        $this->approveFormRenderer = $approveFormRenderer;
        $this->captureFormRenderer = $captureFormRenderer;
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
        if (!$entity instanceof \Darvin\PaymentBundle\Entity\Payment) {
            throw new \LogicException('This widget can be used for Payment only');
        }

        $content = $this->approveFormRenderer->renderForm($entity) ?? '';
        $content .= $this->captureFormRenderer->renderForm($entity) ?? '';

        return $content;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredPermissions(): iterable
    {
        yield Permission::EDIT;
    }
}
