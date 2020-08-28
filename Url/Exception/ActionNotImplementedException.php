<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Url\Exception;

/**
 * Exception for action is not implemented
 */
class ActionNotImplementedException extends \Exception
{
    /**
     * @var string
     */
    private $actionName;

    /**
     * ActionNotImplementedException constructor.
     *
     * @param string $actionName
     */
    public function __construct(string $actionName)
    {
        parent::__construct(sprintf(
            'Method "%s" of UrlBuilderInterface is not implemented yet',
            $actionName
        ));

        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }
}
