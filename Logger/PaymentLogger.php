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

use Darvin\PaymentBundle\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Payment Logger
 */
class PaymentLogger implements LoggerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;

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
    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = []): void
    {
        $payment = $context['payment'] ?? null;

        if ($payment instanceof \Darvin\PaymentBundle\Entity\Payment) {
            $log = new Log($level, $message);
            $payment->addLog($log);

            $this->em->persist($log);
            $this->em->flush();
        }

        if (null !== $this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
