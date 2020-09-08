<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Factory;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\MailerBundle\Model\EmailType;
use Darvin\PaymentBundle\Config\PaymentConfigInterface;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\State\Model\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Email factory
 */
class EmailFactory implements EmailFactoryInterface
{
    /**
     * @var \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface
     */
    protected $genericFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\PaymentBundle\Config\PaymentConfigInterface
     */
    protected $paymentConfig;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @param \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface $genericFactory Generic template email factory
     * @param \Doctrine\ORM\EntityManagerInterface                       $em             EntityManagerInterface
     * @param \Darvin\PaymentBundle\Config\PaymentConfigInterface        $paymentConfig  Payment configuration
     * @param \Symfony\Contracts\Translation\TranslatorInterface         $translator     Translator
     */
    public function __construct(
        TemplateEmailFactoryInterface $genericFactory,
        EntityManagerInterface $em,
        PaymentConfigInterface $paymentConfig,
        TranslatorInterface $translator
    ) {
        $this->genericFactory = $genericFactory;
        $this->em = $em;
        $this->paymentConfig = $paymentConfig;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function createPublicEmail(Payment $payment, State $state): Email
    {
        $emailData = $state->getEmail()->getPublicEmail();

        return $this->genericFactory->createEmail(
            EmailType::PUBLIC,
            $payment->getClientEmail(),
            $this->translator->trans($emailData->getSubject(), ['%orderId%' => $payment->getOrderNumber()], 'email'),
            $emailData->getTemplate(),
            [
                'payment' => $payment,
                'content' => $emailData->getContent(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createServiceEmail(Payment $payment, State $state): Email
    {
        $serviceEmails = $this->paymentConfig->getEmailsByStateName($state->getName());

        if (empty($serviceEmails)) {
            throw new CantCreateEmailException(sprintf('Service email for state "%s" is not specified', $state->getName()));
        }

        $emailData = $state->getEmail()->getServiceEmail();
        $order = $this->getOrder($payment);

        return $this->genericFactory->createEmail(
            EmailType::SERVICE,
            $serviceEmails,
            $this->translator->trans($emailData->getSubject(), ['%orderId%' => $payment->getOrderNumber()], 'email'),
            $emailData->getTemplate(),
            [
                'order'   => $order,
                'payment' => $payment,
                'content' => $emailData->getContent()
            ]
        );
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     */
    private function getOrder(Payment $payment): ?object
    {
        return $this->em->find($payment->getOrderEntityClass(), $payment->getOrderId());
    }
}
