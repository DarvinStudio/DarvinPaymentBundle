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
use Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface;
use Darvin\Utils\Flash\FlashNotifierInterface;
use Darvin\Utils\HttpFoundation\AjaxResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var \Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface
     */
    private $formRenderer;

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
     * @param \Darvin\PaymentBundle\Form\Renderer\Admin\OperationFormRendererInterface $formRenderer Operation admin form renderer
     */
    public function setFormRenderer(OperationFormRendererInterface $formRenderer): void
    {
        $this->formRenderer = $formRenderer;
    }

    /**
     * @param string                                    $method     Payment method
     * @param string                                    $transition Payment transition
     * @param string                                    $token      Payment token
     * @param \Symfony\Component\HttpFoundation\Request $request    Request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    private function execute(string $method, string $transition, string $token, Request $request): Response
    {
        $payment = $this->getPaymentByToken($token);

        $bridge  = $this->getBridge($payment->getGateway());
        $gateway = $this->getGateway($payment->getGateway());

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
            [
                'payment' => $payment,
            ]
        );

        $errorMessage = sprintf('Payment server response: %s', $response->getMessage());

        $this->flashNotifier->error($errorMessage);

        if ($request->isXmlHttpRequest()) {
            return new AjaxResponse($this->formRenderer->renderForm($payment, $transition), false, $errorMessage);
        }

        return new RedirectResponse($referer);
    }
}
