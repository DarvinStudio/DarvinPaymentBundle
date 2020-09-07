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
 * Error model
 */
class Error
{
    /**
     * @var string|null
     *
     * @ORM\Column(length=10, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @param string|null $code
     * @param string|null $description
     */
    public function __construct(?string $code, ?string $description)
    {
       $this->code = $code;
       $this->description = $description;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDescription() ?? '';
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
