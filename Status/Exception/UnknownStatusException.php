<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Status\Exception;

/**
 * Class unknown status exception
 */
class UnknownStatusException extends \Exception
{
    /**
     * @var string
     */
    protected $statusName;

    /**
     * @param string $statusName Status name
     */
    public function __construct(string $statusName)
    {
        parent::__construct(sprintf(
            'Unknown status  "%s"',
            $statusName
        ));

        $this->statusName = $statusName;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return $this->statusName;
    }
}
