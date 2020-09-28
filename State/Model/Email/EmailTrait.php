<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Model\Email;

/**
 * Email trait
 */
trait EmailTrait
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string
     */
    private $name;

    /**
     * @param bool   $enabled   Is enabled
     * @param string $template  Template
     * @param string $stateName Subject
     */
    public function __construct(bool $enabled, string $template, string $stateName)
    {
        $this->enabled = $enabled;
        $this->template = $template;
        $this->name = $stateName;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    abstract public function getSubject(): string;

    /**
     * @return string
     */
    abstract public function getContent(): string;
}
