<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Model;

use Darvin\PaymentBundle\State\Model\Email\Email;

/**
 * Payment state model
 */
class State
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Darvin\PaymentBundle\State\Model\Email\Email
     */
    private $email;

    /**
     * @param string                                         $name  Name
     * @param \Darvin\PaymentBundle\State\Model\Email\Email $email Email
     */
    public function __construct(string $name, Email $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Darvin\PaymentBundle\State\Model\Email\Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return sprintf('payment.state.%s', $this->name);
    }
}
