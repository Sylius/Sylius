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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductOptionValueNormalizerSpec extends ObjectBehavior
{
    function let(
        NormalizerInterface $normalizer,
        TranslatableEntityLocaleAssignerInterface $translatableEntityLocaleAssigner,
    ): void {
        $this->beConstructedWith($translatableEntityLocaleAssigner);

        $this->setNormalizer($normalizer);
    }

    function it_is_an_aware_normalizer(): void
    {
        $this->shouldImplement(NormalizerAwareInterface::class);
    }

    function it_supports_only_product_option_value_interface(
        ProductOptionValueInterface $productOptionValue,
        OrderInterface $order,
    ): void {
        $this->supportsNormalization($productOptionValue)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_supports_the_normalizer_has_not_called_yet(ProductOptionValueInterface $productOptionValue): void
    {
        $this
            ->supportsNormalization($productOptionValue, null, [])
            ->shouldReturn(true)
        ;

        $this
            ->supportsNormalization($productOptionValue, null, ['sylius_product_option_value_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_assigns_locale_to_translatable_entity(
        NormalizerInterface $normalizer,
        ProductOptionValueInterface $productOptionValue,
        TranslatableEntityLocaleAssignerInterface $translatableEntityLocaleAssigner,
    ): void {
        $normalizer
            ->normalize($productOptionValue, null, ['sylius_product_option_value_normalizer_already_called' => true])
            ->willReturn([])
        ;

        $translatableEntityLocaleAssigner->assignLocale($productOptionValue)->shouldBeCalled();

        $this->normalize($productOptionValue, null, [])->shouldReturn([]);
    }

    function it_throws_an_exception_if_the_given_object_is_not_a_product_option_value_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [new \stdClass()])
        ;
    }

    function it_throws_an_exception_if_the_normalizer_was_already_called(ProductOptionValueInterface $productOptionValue): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$productOptionValue, null, ['sylius_product_option_value_normalizer_already_called' => true]])
        ;
    }
}
