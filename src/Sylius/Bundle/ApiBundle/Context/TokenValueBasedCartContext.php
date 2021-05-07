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

namespace Sylius\Bundle\ApiBundle\Context;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/** @experimental */
final class TokenValueBasedCartContext implements CartContextInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var string */
    private $newApiRoute;

    public function __construct(
        RequestStack $requestStack,
        OrderRepositoryInterface $orderRepository,
        string $newApiRoute
    ) {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
        $this->newApiRoute = $newApiRoute;
    }

    public function getCart(): OrderInterface
    {
        $request = $this->getMasterRequest();
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

    private function getMasterRequest(): Request
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new CartNotFoundException('There is no master request on request stack.');
        }

        return $masterRequest;
    }

    private function checkApiRequest(Request $request): void
    {
        if (strpos($request->getRequestUri(), $this->newApiRoute) === false) {
            throw new CartNotFoundException('The master request is not an API request.');
        }
    }
}
