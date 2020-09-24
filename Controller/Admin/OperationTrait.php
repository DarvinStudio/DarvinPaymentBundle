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
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Renderer\AbstractFormRenderer;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Operation trait
 */
trait OperationTrait
{
    /**
     * @var \Darvin\AdminBundle\Route\AdminRouterInterface
     */
    protected $adminRouter;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var \Darvin\PaymentBundle\Form\Renderer\AbstractFormRenderer
     */
    protected $formRenderer;

    /**
     * @var \Darvin\Utils\Flash\FlashNotifierInterface
     */
    protected $flashNotifier;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @param string $method     Payment method
     * @param string $transition Payment transition
     * @param string $token      Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function execute(string $method, string $transition, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);
        $bridge  = $this->getBridge($payment->getGateway());
        $gateway = $this->getGateway($payment->getGateway());
        $request = $this->getRequest();

        if (!$this->authorizationChecker->isGranted(Permission::EDIT, $payment)) {
            throw new AccessDeniedException(
                sprintf('You do not have "%s" permission on "%s" class objects.', Permission::EDIT, get_class($payment))
            );
        }

        $redirectUrl = $this->adminRouter->generate($payment, Payment::class, AdminRouterInterface::TYPE_INDEX, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $referer = $request->headers->get('referer', $redirectUrl);

        $this->validateGateway($gateway, $method);
        $this->validatePayment($payment, $transition);

        $parameters = $bridge->{sprintf('%sParameters', $method)}($payment);

        $response = $gateway->{$method}($parameters)->send();

        if ($response->isSuccessful()) {
            $this->workflow->apply($payment, $transition);
            $this->em->flush();

            if (parse_url($referer, PHP_URL_PATH) === parse_url($redirectUrl, PHP_URL_PATH)) {
                $redirectUrl = $referer;
            }

            $successMessage = sprintf('payment.action.%s.success', $method);

            if ($request->isXmlHttpRequest()) {
                return new AjaxResponse(null, true, $successMessage, [], $redirectUrl);
            }

            $this->flashNotifier->success($successMessage);

            return new RedirectResponse($redirectUrl);
        }

        $this->logger->error(
            $this->translator->trans('error.bad_response', [
                '%method%'  => __METHOD__,
                '%code%'    => $response->getCode(),
                '%message%' => $response->getMessage(),
            ], 'payment_event'),
            ['payment' => $payment]
        );

        $errorMessage = sprintf('Payment server response: %s', $response->getMessage());

        $this->flashNotifier->error($errorMessage);

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse($this->formRenderer->renderForm($payment), false, $errorMessage);
        }

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
     * @param \Darvin\PaymentBundle\Form\Renderer\AbstractFormRenderer $formRenderer View widget pool
     */
    public function setFormRenderer(AbstractFormRenderer $formRenderer): void
    {
        $this->formRenderer = $formRenderer;
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

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getRequest(): Request
    {
        if (null === $this->requestStack->getCurrentRequest()) {
            throw new NotFoundHttpException();
        }

        return $this->requestStack->getCurrentRequest();
    }
}
