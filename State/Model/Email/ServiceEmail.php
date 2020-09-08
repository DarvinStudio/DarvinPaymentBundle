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
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $content;

    /**
     * @param bool   $enabled   Is enabled
     * @param string $template  Template
     * @param string $stateName Subject
     */
    public function __construct(bool $enabled, string $template, string $stateName)
    {
        $this->enabled = $enabled;
        $this->template = $template;
        $this->subject = sprintf('payment.service.%s.subject', $stateName);
        $this->content = sprintf('payment.service.%s.content', $stateName);
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
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
