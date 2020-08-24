<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Status\Model\Email;

/**
 * Model for order type "Service email"
 */
class ServiceEmail
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
     * @param bool   $enabled  Is enabled
     * @param string $template Template
     */
    public function __construct(bool $enabled, string $template)
    {
        $this->enabled = $enabled;
        $this->template = $template;
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
}
