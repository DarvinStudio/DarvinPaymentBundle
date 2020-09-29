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

/**
 * Payment state
 */
class State
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \Darvin\PaymentBundle\State\Model\Email
     */
    private $publicEmail;

    /**
     * @var \Darvin\PaymentBundle\State\Model\Email
     */
    private $serviceEmail;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string                                  $name         Name
     * @param \Darvin\PaymentBundle\State\Model\Email $publicEmail  Public email
     * @param \Darvin\PaymentBundle\State\Model\Email $serviceEmail Service email
     * @param string|null                             $title        Title
     */
    public function __construct(string $name, Email $publicEmail, Email $serviceEmail, ?string $title = null)
    {
        $this->name = $name;
        $this->publicEmail = $publicEmail;
        $this->serviceEmail = $serviceEmail;

        if (null === $title) {
            $title = sprintf('payment.state.%s', $name);
        }

        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \Darvin\PaymentBundle\State\Model\Email
     */
    public function getPublicEmail(): Email
    {
        return $this->publicEmail;
    }

    /**
     * @return \Darvin\PaymentBundle\State\Model\Email
     */
    public function getServiceEmail(): Email
    {
        return $this->serviceEmail;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
