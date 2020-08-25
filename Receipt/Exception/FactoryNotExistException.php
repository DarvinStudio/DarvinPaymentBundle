<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt\Exception;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Exception for case can't get receipt factory
 */
class FactoryNotExistException extends \Exception
{
    /**
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface $payment
     */
    public function __construct(PaymentInterface $payment)
    {
        parent::__construct(sprintf(
            'Can\'t get receipt factory for payment class "%s"',
            get_class($payment)
        ));
    }
}
