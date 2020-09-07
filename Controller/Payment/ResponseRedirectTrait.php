<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller\Payment;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Redirect\RedirectFactoryInterface;
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response redirect trait
 *
 * @property \Doctrine\ORM\EntityManagerInterface                 $em         Entity manager
 * @property \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder URL builder
 * @property \Symfony\Component\Workflow\WorkflowInterface        $workflow   Workflow
 * @property \Darvin\PaymentBundle\Logger\PaymentLoggerInterface  $logger     Logger
 * @property \Twig\Environment                                    $twig       Twig
 */
trait ResponseRedirectTrait
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface
     */
    protected $redirectFactory;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory From factory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface $redirectFactory Redirect factory
     */
    public function setRedirectFactory(RedirectFactoryInterface $redirectFactory): void
    {
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \LogicException
     */
    protected function createPaymentResponse(Payment $payment): Response
    {
        if (!$payment->hasRedirect()) {
            throw new \LogicException('Redirect could not be empty');
        }

        $redirect = $payment->getRedirect();

        if ($payment->getRedirect()->isExpired()) {
            $this->workflow->apply($payment, Transitions::EXPIRE);
            $this->em->flush();

            $this->logger->saveErrorLog($payment, null, 'Payment session expired');

            return new RedirectResponse($this->urlBuilder->getFailUrl($payment));
        }

        if ($redirect->getMethod() !== 'POST') {
            return new RedirectResponse($redirect->getUrl());
        }

        $form = $this->formFactory->create(GatewayRedirectType::class, $redirect->getData(), [
            'action' => $redirect->getUrl(),
            'method' => $redirect->getMethod(),
        ]);

        return new Response(
            $this->twig->render('@DarvinPayment/payment/purchase.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
