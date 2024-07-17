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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $productVariantRepository,
            $orderModifier,
            $cartItemFactory,
            $orderItemQuantityModifier,
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_adds_simple_product_to_cart(
        OrderRepositoryInterface $orderRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderInterface $cart,
        OrderItemInterface $cartItem,
        ProductVariantInterface $productVariant,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $productVariantRepository
            ->findOneBy(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($productVariant)
        ;

        $cartItemFactory->createNew()->willReturn($cartItem);

        $cartItem->setVariant($productVariant)->shouldBeCalled();

        $orderItemQuantityModifier->modify($cartItem, 5)->shouldBeCalled();
        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();

        $this(AddItemToCart::createFromData(
            'TOKEN',
            'PRODUCT_VARIANT_CODE',
            5,
        ))->shouldReturn($cart);
    }

    function it_throws_an_exception_if_product_is_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PRODUCT_VARIANT_CODE'])->willReturn(null);

        $cartItemFactory->createNew()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [AddItemToCart::createFromData(
                'TOKEN',
                'PRODUCT_VARIANT_CODE',
                1,
            )])
        ;
    }

    function it_throws_an_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        CartItemFactoryInterface $cartItemFactory,
        ProductVariantInterface $productVariant,
    ): void {
        $productVariantRepository
            ->findOneBy(['code' => 'PRODUCT_VARIANT_CODE'])
            ->willReturn($productVariant)
        ;

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn(null);

        $cartItemFactory->createNew()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [AddItemToCart::createFromData(
                'TOKEN',
                'PRODUCT_VARIANT_CODE',
                1,
            )])
        ;
    }
}
