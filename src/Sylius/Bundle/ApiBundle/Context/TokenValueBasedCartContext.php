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

namespace Sylius\Bundle\ApiBundle\Context;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TokenValueBasedCartContext implements CartContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private OrderRepositoryInterface $orderRepository,
        private string $newApiRoute,
    ) {
    }

    public function getCart(): OrderInterface
    {
        $request = $this->getMainRequest();
        $this->checkApiRequest($request);

        $tokenValue = $request->attributes->get('tokenValue');
        if ($tokenValue === null) {
            throw new CartNotFoundException('Sylius was not able to find the cart, as there is no passed token value.');
        }

        $cart = $this->orderRepository->findCartByTokenValue($tokenValue);
        if (null === $cart) {
            throw new CartNotFoundException('Sylius was not able to find the cart for passed token value.');
        }

        return $cart;
    }

    private function getMainRequest(): Request
    {
        $mainRequest = $this->requestStack->getMainRequest();
        if (null === $mainRequest) {
            throw new CartNotFoundException('There is no main request on request stack.');
        }

        return $mainRequest;
    }

    private function checkApiRequest(Request $request): void
    {
        if (!str_contains($request->getRequestUri(), $this->newApiRoute)) {
            throw new CartNotFoundException('The main request is not an API request.');
        }
    }
}
