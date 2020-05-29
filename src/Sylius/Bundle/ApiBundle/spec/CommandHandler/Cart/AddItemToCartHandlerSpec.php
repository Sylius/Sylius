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

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\PickupCart;
use Sylius\Bundle\ApiBundle\Command\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AddItemToCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $productRepository,
            $orderModifier,
            $cartItemFactory,
            $orderItemQuantityModifier,
            $orderProcessor
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_adds_simple_product_to_cart(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderProcessorInterface $orderProcessor,
        OrderInterface $cart,
        OrderItemInterface $cartItem,
        ProductInterface $product
    ): void {
        $orderRepository->findOneBy(['state' => OrderInterface::STATE_CART, 'tokenValue' => 'TOKEN'])->willReturn($cart);
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);

        $cartItemFactory->createForProduct($product)->willReturn($cartItem);

        $orderItemQuantityModifier->modify($cartItem, 5)->shouldBeCalled();
        $orderModifier->addToOrder($cart, $cartItem)->shouldBeCalled();

        $orderProcessor->process($cart)->shouldBeCalled();

        $this(AddItemToCart::createFromData('TOKEN', 'PRODUCT_CODE', 5))->shouldReturn($cart);
    }

    function it_throws_an_exception_if_product_is_not_found(
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn(null);

        $cartItemFactory->createForProduct(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [AddItemToCart::createFromData('TOKEN', 'PRODUCT_CODE', 1)])
        ;
    }

    function it_throws_an_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        CartItemFactoryInterface $cartItemFactory,
        ProductInterface $product
    ): void {
        $productRepository->findOneByCode('PRODUCT_CODE')->willReturn($product);
        $orderRepository->findOneBy(['state' => OrderInterface::STATE_CART, 'tokenValue' => 'TOKEN'])->willReturn(null);

        $cartItemFactory->createForProduct(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [AddItemToCart::createFromData('TOKEN', 'PRODUCT_CODE', 1)])
        ;
    }
}
