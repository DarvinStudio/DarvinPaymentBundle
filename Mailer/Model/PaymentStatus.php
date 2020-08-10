<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Model;

use Darvin\PaymentBundle\Mailer\Model\Email\Email;

/**
 * Payment status model
 */
class PaymentStatus
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Darvin\PaymentBundle\Mailer\Model\Email\Email
     */
    private $email;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string                                         $name  Name
     * @param \Darvin\PaymentBundle\Mailer\Model\Email\Email $email Email
     */
    public function __construct(string $name, Email $email)
    {
        $this->name = $name;
        $this->email = $email;

        $this->title = sprintf('darvin_payment.status.%s', $name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Darvin\PaymentBundle\Mailer\Model\Email\Email
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
        return $this->title;
    }
}
