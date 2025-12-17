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

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

final readonly class CheckoutStateUrlGenerator implements CheckoutStateUrlGeneratorInterface
{
    /** @param array<string, mixed> $routeCollection */
    public function __construct(
        private RouterInterface $router,
        private array $routeCollection,
    ) {
    }

    /** @param array<string, mixed> $parameters */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function generateForOrderCheckoutState(
        OrderInterface $order,
        array $parameters = [],
        int $referenceType = self::ABSOLUTE_PATH,
    ): string {
        if (!isset($this->routeCollection[$order->getCheckoutState()]['route'])) {
            throw new RouteNotFoundException();
        }

        return $this->router->generate($this->routeCollection[$order->getCheckoutState()]['route'], $parameters, $referenceType);
    }

    public function generateForCart(array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if (!isset($this->routeCollection['empty_order']['route'])) {
            throw new RouteNotFoundException();
        }

        return $this->router->generate($this->routeCollection['empty_order']['route'], $parameters, $referenceType);
    }

    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }
}
