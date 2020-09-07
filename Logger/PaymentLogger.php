<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Logger;

use Darvin\PaymentBundle\Entity\Error;
use Darvin\PaymentBundle\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Logger
 */
class PaymentLogger implements PaymentLoggerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em Redirect factory
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function saveErrorLog(?Payment $payment, ?string $code, ?string $message): void
    {
        if (null !== $this->logger) {
            $this->logger->error($message);
        }

        // TODO нужна возможность добавлять несколько ошибок
//        if (null !== $payment) {
//            $payment->setError(new Error($code, $message));
//
//            $this->em->flush();
//        }
    }
}
