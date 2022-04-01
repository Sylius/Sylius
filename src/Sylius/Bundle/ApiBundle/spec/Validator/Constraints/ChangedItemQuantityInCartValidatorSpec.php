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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCart;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ChangedItemQuantityInCartValidatorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker
    ): void {
        $this->beConstructedWith($orderItemRepository, $orderRepository, $availabilityChecker);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_change_item_quantity_in_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new AddingEligibleProductVariantToCart()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_changed_item_quantity_in_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new ChangeItemQuantityInCart(2),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_product_variant_does_not_exist(
        RepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn(null);
        $orderItem->getVariantName()->willReturn('MacPro');

        $executionContext
            ->addViolation('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'MacPro'])
            ->shouldBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('token', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }

    function it_adds_violation_if_product_is_disabled(
        RepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getVariantName()->willReturn('Variant Name');

        $productVariant->getProduct()->willReturn($product);
        $productVariant->getCode()->willReturn('VARIANT_CODE');

        $product->isEnabled()->willReturn(false);
        $product->getName()->willReturn('PRODUCT NAME');

        $executionContext
            ->addViolation('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
            ->shouldBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('token', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }

    function it_adds_violation_if_product_variant_is_disabled(
        RepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getVariantName()->willReturn('Variant Name');

        $productVariant->getProduct()->willReturn($product);
        $productVariant->getCode()->willReturn('VARIANT_CODE');

        $product->isEnabled()->willReturn(true);
        $product->getName()->willReturn('PRODUCT NAME');

        $productVariant->isEnabled()->willReturn(false);

        $executionContext
            ->addViolation('sylius.product_variant.not_longer_available', ['%productVariantName%' => 'Variant Name'])
            ->shouldBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('token', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }

    function it_adds_violation_if_product_variant_stock_is_not_sufficient(
        RepositoryInterface $orderItemRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductInterface $product
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getVariantName()->willReturn('Variant Name');

        $productVariant->getProduct()->willReturn($product);
        $productVariant->getCode()->willReturn('VARIANT_CODE');

        $product->isEnabled()->willReturn(true);
        $product->getName()->willReturn('PRODUCT NAME');

        $productVariant->isEnabled()->willReturn(true);

        $availabilityChecker->isStockSufficient($productVariant, 2)->willReturn(false);

        $executionContext
            ->addViolation('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'VARIANT_CODE'])
            ->shouldBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('token', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }

    function it_adds_violation_if_product_is_not_available_in_channel(
        RepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductInterface $product,
        ChannelInterface $channel,
        OrderInterface $cart
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getVariantName()->willReturn('Variant Name');

        $productVariant->getProduct()->willReturn($product);
        $productVariant->getCode()->willReturn('VARIANT_CODE');

        $product->isEnabled()->willReturn(true);
        $product->getName()->willReturn('PRODUCT NAME');

        $productVariant->isEnabled()->willReturn(true);

        $availabilityChecker->isStockSufficient($productVariant, 2)->willReturn(true);

        $product->getName()->willReturn('PRODUCT NAME');

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $cart->getChannel()->willReturn($channel);

        $product->hasChannel($channel)->willReturn(false);

        $executionContext
            ->addViolation('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
            ->shouldBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('TOKEN', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }

    function it_does_nothing_if_product_and_variant_are_enabled_and_available_in_channel(
        RepositoryInterface $orderItemRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductInterface $product,
        ChannelInterface $channel,
        OrderInterface $cart
    ): void {
        $this->initialize($executionContext);

        $orderItemRepository->findOneBy(['id' => '11'])->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getVariantName()->willReturn('Variant Name');

        $productVariant->getProduct()->willReturn($product);
        $productVariant->getCode()->willReturn('VARIANT_CODE');

        $product->isEnabled()->willReturn(true);
        $product->getName()->willReturn('PRODUCT NAME');

        $productVariant->isEnabled()->willReturn(true);

        $availabilityChecker->isStockSufficient($productVariant, 2)->willReturn(true);

        $product->getName()->willReturn('PRODUCT NAME');

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $cart->getChannel()->willReturn($channel);

        $product->hasChannel($channel)->willReturn(true);

        $executionContext
            ->addViolation('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldNotBeCalled()
        ;
        $executionContext
            ->addViolation('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
            ->shouldNotBeCalled()
        ;
        $executionContext
            ->addViolation('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldNotBeCalled()
        ;

        $this->validate(
            ChangeItemQuantityInCart::createFromData('TOKEN', '11', 2),
            new ChangedItemQuantityInCart()
        );
    }
}
