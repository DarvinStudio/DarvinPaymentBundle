<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Exception;

/**
 * Class unknown state exception
 */
class UnknownStateException extends \Exception
{
    /**
     * @var string
     */
    private $stateName;

    /**
     * @param string $stateName State name
     */
    public function __construct(string $stateName)
    {
        parent::__construct(sprintf(
            'Unknown state  "%s"',
            $stateName
        ));

        $this->stateName = $stateName;
    }

    /**
     * @return string
     */
    public function getStateName(): string
    {
        return $this->stateName;
    }
}
