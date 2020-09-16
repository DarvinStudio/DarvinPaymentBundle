<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Redirect
{
    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $url;

    /**
     * @var string|null
     *
     * @ORM\Column(length=10, nullable=true)
     */
    protected $method;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json", nullable=true)
     */
    protected $data;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expiryDate;

    /**
     * Redirect constructor.
     *
     * @param string|null    $url        Url
     * @param string|null    $method     Method
     * @param array|null     $data       Data
     * @param \DateTime|null $expiryDate Expire Date
     */
    public function __construct(?string $url = null, ?string $method = null, ?array $data = null, ?\DateTime $expiryDate = null)
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUrl() ?? 'Redirect data is empty';
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->getUrl() === null;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->getExpiryDate() === null) {
            return false;
        }

        return $this->getExpiryDate()->getTimestamp() < (new \DateTime)->getTimestamp();
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiryDate(): ?\DateTime
    {
        return $this->expiryDate;
    }
}
