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

use Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Bundle\CoreBundle\MessageDispatcher\ResendOrderConfirmationEmailDispatcherInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResendOrderConfirmationEmailAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderEmailManagerInterface|ResendOrderConfirmationEmailDispatcherInterface $orderEmailManager,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack|SessionInterface $requestStackOrSession,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }

        if ($this->orderEmailManager instanceof OrderEmailManagerInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                OrderEmailManagerInterface::class,
                self::class,
                ResendOrderConfirmationEmailDispatcherInterface::class,
            );

            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'The argument name $orderEmailManager is deprecated and will be renamed to $resendOrderConfirmationEmailDispatcher in Sylius 2.0.',
            );
        }
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

        $this->sendConfirmationEmailOrDispatchResendOrderConfirmation($order);

        FlashBagProvider
            ::getFlashBag($this->requestStackOrSession)
            ->add('success', 'sylius.email.order_confirmation_resent')
        ;

        return new RedirectResponse($request->headers->get('referer'));
    }

    private function sendConfirmationEmailOrDispatchResendOrderConfirmation(OrderInterface $order): void
    {
        if ($this->orderEmailManager instanceof OrderEmailManagerInterface) {
            $this->orderEmailManager->sendConfirmationEmail($order);
        } else {
            $this->orderEmailManager->dispatch($order);
        }
    }
}
