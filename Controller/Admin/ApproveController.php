<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller\Admin;

use Darvin\AdminBundle\Route\AdminRouterInterface;
use Darvin\AdminBundle\Security\Permissions\Permission;
use Darvin\PaymentBundle\Controller\AbstractController;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Payment\Operations;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Approve admin controller
 */
class ApproveController extends AbstractController
{
    /**
     * @var \Darvin\AdminBundle\Route\AdminRouterInterface
     */
    private $adminRouter;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Darvin\Utils\Flash\FlashNotifierInterface
     */
    private $flashNotifier;

    /**
     * @param \Darvin\AdminBundle\Route\AdminRouterInterface $adminRouter Admin router
     */
    public function setAdminRouter(AdminRouterInterface $adminRouter): void
    {
        $this->adminRouter = $adminRouter;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker Authorization checker
     */
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker): void
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param \Darvin\Utils\Flash\FlashNotifierInterface $flashNotifier Flash notifier
     */
    public function setFlashNotifier(FlashNotifierInterface $flashNotifier): void
    {
        $this->flashNotifier = $flashNotifier;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $token   Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function __invoke(Request $request, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $this->validatePayment($payment, Operations::APPROVE);

        if (!$this->authorizationChecker->isGranted(Permission::EDIT, $payment)) {
            throw new AccessDeniedException(
                sprintf('You do not have "%s" permission on "%s" class objects.', Permission::EDIT, get_class($payment))
            );
        }

        $referer = $request->headers->get(
            'referer',
            $this->adminRouter->generate($payment, Payment::class, AdminRouterInterface::TYPE_INDEX, [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $this->workflow->apply($payment, Operations::APPROVE);

        $this->em->flush();

        $successMessage = 'payment.action.approve.success';

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse(null, true, $successMessage, [], $referer);
        }

        $this->flashNotifier->success($successMessage);

        return new RedirectResponse($referer);
    }
}
