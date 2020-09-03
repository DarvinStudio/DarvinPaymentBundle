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
use Darvin\PaymentBundle\Url\Exception\ActionNotImplementedException;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;

/**
 * Capture view widget
 */
class PaymentCaptureWidget extends AbstractWidget
{
    public const ALIAS = 'payment_capture';

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder Url builder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
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
        $state = $this->getPropertyValue($entity, $options['property']);

        if (PaymentStateType::AUTHORIZED !== $state) {
            return null;
        }

        try {
            $url = $this->urlBuilder->getCaptureUrl($entity, 'sberbank');
        } catch (ActionNotImplementedException $ex) {
            return sprintf('<p>%s</p>', $ex->getMessage());
        }

        return $this->twig->render('@DarvinPayment/admin/widget/capture_form.html.twig',[
            'url' => $url,
            'id'  => $entity->getId(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequiredPermissions(): iterable
    {
        yield Permission::EDIT;
    }
}
