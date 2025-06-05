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

namespace Tests\Sylius\Bundle\CoreBundle\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGenerator;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class CheckoutStateUrlGeneratorTest extends TestCase
{
    private MockObject&RouterInterface $router;

    private CheckoutStateUrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);

        $routeCollection = [
            'addressed' => ['route' => 'sylius_shop_checkout_select_shipping'],
            'empty_order' => ['route' => 'sylius_shop_cart_summary'],
        ];

        $this->urlGenerator = new CheckoutStateUrlGenerator($this->router, $routeCollection);
    }

    public function testAUrlGenerator(): void
    {
        $this->assertInstanceOf(UrlGeneratorInterface::class, $this->urlGenerator);
    }

    public function testACheckoutStateUrlGenerator(): void
    {
        $this->assertInstanceOf(CheckoutStateUrlGeneratorInterface::class, $this->urlGenerator);
    }

    public function testGeneratesStateUrl(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $order->method('getCheckoutState')->willReturn('addressed');

        $this->router->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_checkout_select_shipping', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/checkout/address')
        ;

        $url = $this->urlGenerator->generateForOrderCheckoutState($order);

        $this->assertSame('/checkout/address', $url);
    }

    public function testARegularUrlGenerator(): void
    {
        $this->router->expects($this->once())
            ->method('generate')
            ->with('route_name', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/some-route')
        ;

        $url = $this->urlGenerator->generate('route_name');

        $this->assertSame('/some-route', $url);
    }

    public function testThrowsRouteNotFoundExceptionIfThereIsNoRouteForGivenState(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $order->method('getCheckoutState')->willReturn('shipping_selected');

        $this->router->expects($this->never())->method('generate');

        $this->expectException(RouteNotFoundException::class);

        $this->urlGenerator->generateForOrderCheckoutState($order);
    }

    public function testGeneratesCartUrl(): void
    {
        $this->router->expects($this->once())
            ->method('generate')
            ->with('sylius_shop_cart_summary', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/cart')
        ;

        $url = $this->urlGenerator->generateForCart();

        $this->assertSame('/cart', $url);
    }
}
