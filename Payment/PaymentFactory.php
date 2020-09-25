<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment;

use Darvin\PaymentBundle\Entity\Client;
use Darvin\PaymentBundle\Entity\PaidOrder;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Workflow\Transitions;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Payment factory
 */
class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    private $entityResolver;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    private $workflow;

    /**
     * @var bool
     */
    private $autoApproval;

    /**
     * @var string
     */
    private $defaultCurrency;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                      $entityManager   Entity manager
     * @param \Darvin\Utils\ORM\EntityResolverInterface                 $entityResolver  Entity resolver
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator       Validator
     * @param \Symfony\Component\Workflow\WorkflowInterface             $workflow        Workflow for payment state
     * @param bool                                                      $autoApproval    Auto approval or need admin approval
     * @param string                                                    $defaultCurrency Currency by default
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityResolverInterface $entityResolver,
        ValidatorInterface $validator,
        WorkflowInterface $workflow,
        bool $autoApproval,
        string $defaultCurrency
    ) {
        $this->entityManager = $entityManager;
        $this->entityResolver = $entityResolver;
        $this->validator = $validator;
        $this->workflow = $workflow;
        $this->autoApproval = $autoApproval;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritDoc}
     */
    public function createPayment(PaidOrder $order, string $amount, ?Client $client = null, ?string $currency = null): Payment
    {
        if (null === $currency) {
            $currency = $this->defaultCurrency;
        }

        $class = $this->entityResolver->resolve(Payment::class);

        /** @var \Darvin\PaymentBundle\Entity\Payment $payment */
        $payment = new $class($order, $amount, $currency);

        if (null !== $client) {
            $payment->setClient($client);
        }

        $this->workflow->getMarking($payment);

        $payment->setToken(Uuid::uuid4()->toString());

        $this->validate($payment);

        if ($this->autoApproval) {
            $this->workflow->apply($payment, Transitions::APPROVE);
        }

        return $payment;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @throws \InvalidArgumentException
     */
    private function validate(Payment $payment): void
    {
        $violations = $this->validator->validate($payment);

        if ($violations->count() > 0) {
            $errors = [];

            /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $errors[] = implode(': ', [$violation->getPropertyPath(), $violation->getMessage()]);
            }

            throw new \InvalidArgumentException(sprintf('Errors: "%s"', implode('", "', $errors)));
        }
    }
}
