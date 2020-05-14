<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 13:49
 */

namespace Darvin\PaymentBundle\UrlBuilder\Exception;


class ActionNotImplementedException extends \Exception
{
    private $actionName;

    /**
     * ActionNotImplementedException constructor.
     *
     * @param string $actionName
     */
    public function __construct($actionName)
    {
        parent::__construct(sprintf(
            "Method %s is not implemented yet",
            $actionName
        ));
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}
