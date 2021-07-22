<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt\Factory\YooKassa;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\YookassaBridge;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Receipt\Exception\CantCreateReceiptException;
use Darvin\PaymentBundle\Receipt\Model\YooKassa\Receipt;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * YooKassa receipt factory
 */
class YooKassaReceiptFactory implements ReceiptFactoryInterface
{
    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    /**
     * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function createReceipt(Payment $payment, BridgeInterface $bridge): array
    {
        $receipt = new Receipt();

        $this->validate($receipt, $payment);

        return $receipt->getData();
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Payment $payment, BridgeInterface $bridge): bool
    {
        return $bridge instanceof YookassaBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'yookassa';
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Receipt $receipt
     * @param \Darvin\PaymentBundle\Entity\Payment                 $payment
     *
     * @throws \Darvin\PaymentBundle\Receipt\Exception\CantCreateReceiptException
     */
    private function validate(Receipt $receipt, Payment $payment): void
    {
        $errors = $this->validator->validate($receipt);

        if ($errors->count() > 0) {
            $message = implode(' ', array_map(function (ConstraintViolationInterface $error): string {
                return implode(': ', [$error->getPropertyPath(), $error->getMessage()]);
            }, iterator_to_array($errors)));

            throw new CantCreateReceiptException($payment, $message);
        }
    }
}
