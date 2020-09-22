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
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\HttpFoundation\Response;

/**
 * Void controller
 */
class VoidController extends AbstractController
{
    use OperationTrait;

    /**
     * @param string $token Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function __invoke(string $token): Response
    {
        return $this->execute('void', Transitions::VOID, $token);
    }
}
