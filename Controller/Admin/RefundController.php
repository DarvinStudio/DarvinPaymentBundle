<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller\Admin;

use Darvin\PaymentBundle\Controller\AbstractController;
use Darvin\PaymentBundle\Payment\Operations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Refund admin controller
 */
class RefundController extends AbstractController
{
    use OperationTrait;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request Request
     * @param string                                    $token   Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request, string $token): Response
    {
        return $this->execute('refund', Operations::REFUND, $token, $request);
    }
}
