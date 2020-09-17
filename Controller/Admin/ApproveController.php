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
use Darvin\PaymentBundle\Workflow\Transitions;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Approve controller
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
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function __invoke(string $token): Response
    {
        $payment = $this->getPaymentByToken($token);
        $request = $this->requestStack->getCurrentRequest();

        $this->validatePayment($payment, Transitions::APPROVE);

        if (!$this->authorizationChecker->isGranted(Permission::EDIT, $payment)) {
            throw new AccessDeniedException(
                sprintf('You do not have "%s" permission on "%s" class objects.', Permission::EDIT, get_class($payment))
            );
        }

        $referer = $request->headers->get(
            'referer',
            $this->adminRouter->generate($payment, Payment::class, AdminRouterInterface::TYPE_INDEX, [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        $this->workflow->apply($payment, Transitions::APPROVE);
        $this->em->flush();

        $this->logChangedState($payment);

        $successMessage = 'payment.action.approve.success';

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse(null, true, $successMessage, [], $referer);
        }

        $this->flashNotifier->success($successMessage);

        return new RedirectResponse($referer);
    }

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
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }
}
