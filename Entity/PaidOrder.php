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
 * @ORM\Embeddable
 */
class PaidOrder
{
    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $class;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $number;

    /**
     * Redirect constructor.
     *
     * @param string $id     Id
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
        return $this->getId();
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
