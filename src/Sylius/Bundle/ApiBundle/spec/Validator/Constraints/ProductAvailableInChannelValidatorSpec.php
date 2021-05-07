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
use Sylius\Bundle\ApiBundle\Validator\Constraints\ProductAvailableInChannel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ProductAvailableInChannelValidatorSpec extends ObjectBehavior
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

    function it_throws_an_exception_if_value_is_not_an_instance_of_product_or_variant_enabled_class(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new CompleteOrder(), new class() extends Constraint {
            }]);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_add_item_to_cart(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }]);
    }

    function it_adds_violation_if_product_is_not_available_in_channel(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productCode', 'productVariantCode', 1);
        $value->setOrderTokenValue('TOKEN');
        $constraint = new ProductAvailableInChannel();
        $constraint->message = 'message';

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $cart->getChannel()->willReturn($channel);

        $productVariant->getProduct()->willReturn($product);

        $product->hasChannel($channel)->willReturn(false);
        $product->getName()->willReturn('PRODUCTNAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'PRODUCTNAME']
            )
            ->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_nothing_if_product_is_available_in_channel(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $value = new AddItemToCart('productCode', 'productVariantCode', 1);
        $value->setOrderTokenValue('TOKEN');
        $constraint = new ProductAvailableInChannel();
        $constraint->message = 'message';

        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($cart);
        $productVariantRepository->findOneBy(['code' => 'productVariantCode'])->willReturn($productVariant);
        $cart->getChannel()->willReturn($channel);

        $productVariant->getProduct()->willReturn($product);

        $product->hasChannel($channel)->willReturn(true);
        $product->getName()->willReturn('PRODUCTNAME');
        $executionContext
            ->addViolation(
                'message',
                ['%productName%' => 'PRODUCTNAME']
            )
            ->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

}
