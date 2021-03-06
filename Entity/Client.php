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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Client
 *
 * @ORM\Embeddable
 */
class Client
{
    /**
     * @var string|null
     *
     * @ORM\Column(length=32, nullable=true)
     *
     * @Assert\Length(max=32)
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(length=128, nullable=true)
     *
     * @Assert\Length(max=128)
     */
    private $class;

    /**
     * @var string|null
     *
     * @ORM\Column(length=50, nullable=true)
     *
     * @Assert\Email
     * @Assert\Length(max=50)
     */
    private $email;

    /**
     * @param string|null $id    ID
     * @param string|null $class Entity class
     * @param string|null $email Email
     */
    public function __construct(?string $id = null, ?string $class = null, ?string $email = null)
    {
        $this->id = $id;
        $this->class = $class;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->id;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
