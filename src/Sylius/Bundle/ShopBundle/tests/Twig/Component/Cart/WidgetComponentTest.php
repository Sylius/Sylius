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

namespace Tests\Sylius\Bundle\ShopBundle\Twig\Component\Cart;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Twig\Component\Cart\WidgetComponent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;

final class WidgetComponentTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private WidgetComponent $widgetComponent;

    protected function setUp(): void
    {
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);

        $this->widgetComponent = new WidgetComponent($this->cartContext, $this->orderRepository);
    }

    public function testRestoresTheCurrentCartWhenTheHydratedCartNoLongerExists(): void
    {
        $currentCart = $this->createMock(OrderInterface::class);
        $this->orderRepository->method('find')->with(1)->willReturn(null);
        $this->cartContext->method('getCart')->willReturn($currentCart);

        $this->widgetComponent->cart = $this->widgetComponent->hydrateResource(1);
        $this->widgetComponent->restoreMissingCart();

        $this->assertSame($currentCart, $this->widgetComponent->cart);
    }

    public function testKeepsTheHydratedCartWhenItStillExists(): void
    {
        $cart = $this->createMock(OrderInterface::class);
        $this->orderRepository->method('find')->with(1)->willReturn($cart);
        $this->cartContext->expects($this->never())->method('getCart');

        $this->widgetComponent->cart = $this->widgetComponent->hydrateResource(1);
        $this->widgetComponent->restoreMissingCart();

        $this->assertSame($cart, $this->widgetComponent->cart);
    }
}
