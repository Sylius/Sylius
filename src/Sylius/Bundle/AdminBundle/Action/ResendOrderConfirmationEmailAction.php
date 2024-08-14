<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Action;

use Sylius\Bundle\CoreBundle\CommandDispatcher\ResendOrderConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final readonly class ResendOrderConfirmationEmailAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ResendOrderConfirmationEmailDispatcherInterface $resendOrderConfirmationEmailDispatcher,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack $requestStack,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $orderId = $request->attributes->get('id', '');

        if (!$this->csrfTokenManager->isTokenValid(
            new CsrfToken($orderId, (string) $request->query->get('_csrf_token', '')),
        )) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOrderById($orderId);
        if ($order === null) {
            throw new NotFoundHttpException(sprintf('The order with id %s has not been found', $orderId));
        }

        $this->resendOrderConfirmationEmailDispatcher->dispatch($order);

        FlashBagProvider
            ::getFlashBag($this->requestStack)
            ->add('success', 'sylius.email.order_confirmation_resent')
        ;

        return $this->redirect($request);
    }

    private function redirect(Request $request): RedirectResponse
    {
        $redirect = $request->attributes->get('_sylius', [])['redirect'] ?? 'referer';

        return new RedirectResponse($this->router->generate($redirect));
    }
}
