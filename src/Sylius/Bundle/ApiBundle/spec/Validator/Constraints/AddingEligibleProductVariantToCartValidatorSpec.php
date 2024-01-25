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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddingEligibleProductVariantToCartValidatorSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->beConstructedWith($productVariantRepository, $orderRepository, $availabilityChecker);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_add_item_to_cart_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new AddingEligibleProductVariantToCart()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_adding_eligible_product_variant_to_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new AddItemToCart('productVariantCode', 1),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_product_variant_does_not_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddItemToCart('productVariantCode', 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    function it_adds_violation_if_product_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
    ): void {
        $this->initialize($executionContext);

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(false);
        $product->getName()->willReturn('PRODUCT NAME');

        $executionContext
            ->addViolation('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddItemToCart('productVariantCode', 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    function it_adds_violation_if_product_variant_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
    ): void {
        $this->initialize($executionContext);

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(false);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);

        $executionContext
            ->addViolation('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddItemToCart('productVariantCode', 1),
            new AddingEligibleProductVariantToCart(),
        );
    }

    function it_adds_violation_if_product_variant_stock_is_not_sufficient_and_cart_has_same_units(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderInterface $cart,
        Collection $items,
        OrderItemInterface $orderItem,
        ProductVariantInterface $itemProductVariant,
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $cart->getItems()->willReturn($items->getWrappedObject());

        $productVariant->isTracked()->willReturn(true);

        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($itemProductVariant);
        $orderItem->getQuantity()->willReturn(1);
        $itemProductVariant->getCode()->willReturn('productVariantCode');

        $availabilityChecker->isStockSufficient($productVariant, 2)->willReturn(false);

        $executionContext
            ->addViolation('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    function it_adds_violation_if_product_variant_stock_is_not_sufficient_and_cart_has_not_same_units(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        AvailabilityCheckerInterface $availabilityChecker,
        OrderInterface $cart,
        Collection $items,
        OrderItemInterface $orderItem,
        ProductVariantInterface $orderItemVariant,
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $productVariant->isTracked()->willReturn(true);

        $cart->getItems()->willReturn($items->getWrappedObject());

        $orderItem->getVariant()->willReturn($orderItemVariant);

        $orderItemVariant->getCode()->willReturn('otherProductVariantCode');

        $items->getIterator()->willReturn(new \ArrayIterator([]));

        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(false);

        $executionContext
            ->addViolation('sylius.product_variant.not_sufficient', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate(
            $command,
            new AddingEligibleProductVariantToCart(),
        );
    }

    function it_adds_violation_if_product_is_not_available_in_channel(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        OrderInterface $cart,
        Collection $items,
        OrderItemInterface $orderItem,
        AvailabilityCheckerInterface $availabilityChecker,
        ProductVariantInterface $itemProductVariant,
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $cart->getItems()->willReturn($items->getWrappedObject());

        $productVariant->isTracked()->willReturn(true);

        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($itemProductVariant);
        $orderItem->getQuantity()->willReturn(1);

        $product->isEnabled()->willReturn(true);
        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(true);

        $product->hasChannel($channel)->willReturn(false);
        $product->getName()->willReturn('PRODUCT NAME');

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $cart->getChannel()->willReturn($channel);

        $executionContext
            ->addViolation('sylius.product.not_exist', ['%productName%' => 'PRODUCT NAME'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new AddingEligibleProductVariantToCart());
    }

    function it_does_nothing_if_product_and_variant_are_enabled_and_available_in_channel(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        OrderInterface $cart,
        Collection $items,
        OrderItemInterface $orderItem,
        ProductVariantInterface $itemProductVariant,
        AvailabilityCheckerInterface $availabilityChecker,
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);

        $cart->getItems()->willReturn($items->getWrappedObject());

        $productVariant->isTracked()->willReturn(true);

        $items->getIterator()->willReturn(new \ArrayIterator([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($itemProductVariant);
        $orderItem->getQuantity()->willReturn(1);

        $items->isEmpty()->willReturn(true);

        $product->isEnabled()->willReturn(true);
        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(true);

        $product->hasChannel($channel)->willReturn(true);
        $product->getName()->willReturn('PRODUCT NAME');

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $cart->getChannel()->willReturn($channel);

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

        $this->validate($command, new AddingEligibleProductVariantToCart());
    }
}
