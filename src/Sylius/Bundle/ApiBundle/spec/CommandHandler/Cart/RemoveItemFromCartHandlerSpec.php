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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RemoveItemFromCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        OrderProcessorInterface $orderProcessor,
        ProductVariantResolverInterface $variantResolver
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $productRepository,
            $orderModifier,
            $orderProcessor,
            $variantResolver
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_removes_simple_product_from_cart(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        OrderProcessorInterface $orderProcessor,
        ProductVariantResolverInterface $variantResolver,
        ProductInterface $product,
        OrderInterface $cart,
        ProductVariantInterface $productVariant,
        OrderItemUnitInterface $cartItemUnit,
        OrderItemInterface $cartItem
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);
        $orderRepository->findOneBy(['state' => OrderInterface::STATE_CART, 'tokenValue' => 'TOKEN'])->willReturn($cart);

        $variantResolver->getVariant($product)->willReturn($productVariant);

        $orderItemUnits = new ArrayCollection([$cartItemUnit->getWrappedObject()]);

        $cartItemUnit->getOrderItem()->willReturn($cartItem);

        $cart->getItemUnitsByVariant($productVariant)->willReturn($orderItemUnits);

        $orderModifier->removeFromOrder($cart, $cartItem)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(RemoveItemFromCart::removeFromData('TOKEN', 'PRODUCT_CODE'))->shouldReturn($cart);
    }

    function it_throws_an_exception_if_product_is_not_found(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn(null);

        $orderRepository->findOneBy([
            'state' => OrderInterface::STATE_CART,
            'tokenValue' => 'TOKEN',
        ])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [RemoveItemFromCart::removeFromData('TOKEN', 'PRODUCT_CODE')])
        ;
    }

    function it_throws_an_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        OrderInterface $cart
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);
        $orderRepository->findOneBy(['state' => OrderInterface::STATE_CART, 'tokenValue' => 'TOKEN'])->willReturn(null);

        $cart->getItemUnitsByVariant($productVariant)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [RemoveItemFromCart::removeFromData('TOKEN', 'PRODUCT_CODE')])
        ;
    }
}
