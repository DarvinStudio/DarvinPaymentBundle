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
 * Paid order
 *
 * @ORM\Embeddable
 */
class PaidOrder
{
    /**
     * @var string
     *
     * @ORM\Column(length=32)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=32)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(length=128)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column(length=64)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=64)
     */
    protected $number;

    /**
     * @param string $id     ID
     * @param string $class  Entity class
     * @param string $number Number
     */
    public function __construct(string $id, string $class, string $number)
    {
        $this->id = $id;
        $this->class = $class;
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }
}
