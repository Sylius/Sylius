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

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

final class CheckoutStateUrlGenerator implements CheckoutStateUrlGeneratorInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var array
     */
    private $routeCollection = [];

    /**
     * @param RouterInterface $router
     * @param array $routeCollection
     */
    public function __construct(RouterInterface $router, array $routeCollection)
    {
        $this->router = $router;
        $this->routeCollection = $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->router->generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function generateForOrderCheckoutState(
        OrderInterface $order,
        array $parameters = [],
        int $referenceType = self::ABSOLUTE_PATH
    ): string {
        if (!isset($this->routeCollection[$order->getCheckoutState()]['route'])) {
            throw new RouteNotFoundException();
        }

        return $this->router->generate($this->routeCollection[$order->getCheckoutState()]['route'], $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function generateForCart(array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        if (!isset($this->routeCollection['empty_order']['route'])) {
            throw new RouteNotFoundException();
        }

        return $this->router->generate($this->routeCollection['empty_order']['route'], $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }
}
