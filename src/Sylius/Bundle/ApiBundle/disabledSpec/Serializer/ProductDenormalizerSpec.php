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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_product_denormalizer_already_called';

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this
            ->supportsDenormalization([], ProductInterface::class, context: [self::ALREADY_CALLED => true])
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', ProductInterface::class)->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_product(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_removes_options_from_data_if_given_product_has_variants_defined(
        DenormalizerInterface $denormalizer,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([$productVariant->getWrappedObject()]));

        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize(
                [],
                ProductInterface::class,
                null,
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $product,
                    self::ALREADY_CALLED => true,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->denormalize(
            ['options' => ['/options/color']],
            ProductInterface::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $product],
        );
    }

    function it_does_not_remove_options_from_data_if_given_product_has_np_variants_defined(
        DenormalizerInterface $denormalizer,
        ProductInterface $product,
    ): void {
        $product->getVariants()->willReturn(new ArrayCollection([]));

        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize(
                ['options' => ['/options/color']],
                ProductInterface::class,
                null,
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $product,
                    self::ALREADY_CALLED => true,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->denormalize(
            ['options' => ['/options/color']],
            ProductInterface::class,
            null,
            [AbstractNormalizer::OBJECT_TO_POPULATE => $product],
        );
    }

    function it_does_not_remove_options_from_data_if_there_is_no_object_to_populate_in_context_defined(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);
        $denormalizer
            ->denormalize(
                ['options' => ['/options/color']],
                ProductInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )
            ->shouldBeCalled()
        ;

        $this->denormalize(['options' => ['/options/color']], ProductInterface::class);
    }

    function it_throws_an_exception_if_object_to_populate_is_not_a_product(
        DenormalizerInterface $denormalizer,
        ProductVariantInterface $productVariant,
    ): void {
        $this->setDenormalizer($denormalizer);
        $denormalizer->denormalize([], ProductInterface::class, null, [self::ALREADY_CALLED => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('denormalize', [
                ['options' => ['/options/color']],
                ProductInterface::class,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $productVariant],
            ])
        ;
    }
}
