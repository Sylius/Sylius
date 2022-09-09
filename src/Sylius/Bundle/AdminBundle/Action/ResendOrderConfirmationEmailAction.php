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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ResendOrderConfirmationEmailAction
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderEmailManagerInterface $orderEmailManager,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private SessionInterface|RequestStack $requestStackOrSession,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation('sylius/admin-bundle', '2.0', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius 1.12 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
    }

    public function __invoke(Request $request): Response
    {
        $orderId = $request->attributes->get('id');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken($orderId, (string) $request->query->get('_csrf_token')))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new NotFoundHttpException(sprintf('The order with id %s has not been found', $orderId));
        }

        $this->orderEmailManager->sendConfirmationEmail($order);

        $this->getFlashBag()->add('success', 'sylius.email.order_confirmation_resent');

        return new RedirectResponse($request->headers->get('referer'));
    }

    private function getFlashBag(): FlashBagInterface
    {
        if ($this->requestStackOrSession instanceof RequestStack) {
            return $this->requestStackOrSession->getSession()->getBag('flashes');
        }

        return $this->requestStackOrSession->getBag('flashes');
    }
}
