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
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCart;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddingEligibleProductVariantToCartValidatorSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($productVariantRepository, $orderRepository);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_add_item_to_cart_command(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new AddingEligibleProductVariantToCart()]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_adding_eligible_product_variant_to_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new AddItemToCart('productCode', 'productVariantCode', 1),
                new class() extends Constraint {}
            ])
        ;
    }

    function it_adds_violation_if_product_variant_does_not_exist(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn(null);

        $executionContext
            ->addViolation('sylius.product_variant.not_exist', ['%productVariantCode%' => 'productVariantCode'])
            ->shouldBeCalled()
        ;

        $this->validate(
            new AddItemToCart('productCode', 'productVariantCode', 1),
            new AddingEligibleProductVariantToCart()
        );
    }

    function it_adds_violation_if_product_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product
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
            new AddItemToCart('productCode', 'productVariantCode', 1),
            new AddingEligibleProductVariantToCart()
        );
    }

    function it_adds_violation_if_product_variant_is_disabled(
        ProductVariantRepositoryInterface $productVariantRepository,
        ExecutionContextInterface $executionContext,
        ProductVariantInterface $productVariant,
        ProductInterface $product
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
            new AddItemToCart('productCode', 'productVariantCode', 1),
            new AddingEligibleProductVariantToCart()
        );
    }

    function it_adds_violation_if_product_is_not_available_in_channel(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $executionContext,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        OrderInterface $cart
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productCode', 'productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);
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
        OrderInterface $cart
    ): void {
        $this->initialize($executionContext);

        $command = new AddItemToCart('productCode', 'productVariantCode', 1);
        $command->setOrderTokenValue('TOKEN');

        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $productVariant->getCode()->willReturn('productVariantCode');
        $productVariant->isEnabled()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $product->isEnabled()->willReturn(true);
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

        $this->validate($command, new AddingEligibleProductVariantToCart());
    }
}
