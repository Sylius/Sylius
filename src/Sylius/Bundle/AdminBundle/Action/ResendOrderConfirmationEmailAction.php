<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResendOrderConfirmationEmailAction
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderEmailManagerInterface */
    private $orderEmailManager;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var Session */
    private $session;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderEmailManagerInterface $orderEmailManager,
        CsrfTokenManagerInterface $csrfTokenManager,
        SessionInterface $session
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderEmailManager = $orderEmailManager;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->session = $session;
    }

    public function __invoke(Request $request): Response
    {
        $orderId = $request->attributes->get('id');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($orderId, $request->query->get('_csrf_token')))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new NotFoundHttpException(sprintf('The order with id %s has not been found', $orderId));
        }

        $this->orderEmailManager->sendConfirmationEmail($order);

        $this->session->getFlashBag()->add(
            'success',
            'sylius.email.order_confirmation_resent'
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
