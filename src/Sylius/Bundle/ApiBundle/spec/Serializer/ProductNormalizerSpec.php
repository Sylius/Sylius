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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductNormalizerSpec extends ObjectBehavior
{
    function let(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter);
    }

    function it_supports_only_product_interface(ProductInterface $product, OrderInterface $order): void
    {
        $this->supportsNormalization($product)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(ProductVariantInterface $variant): void
    {
        $this
            ->supportsNormalization($variant, null, ['product_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_adds_default_variant_iri_to_serialized_product(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        NormalizerInterface $normalizer,
        ProductInterface $product,
        ProductVariantInterface $variant
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($product, null, ['product_normalizer_already_called' => true])->willReturn([]);
        $defaultProductVariantResolver->getVariant($product)->willReturn($variant);
        $iriConverter->getIriFromItem($variant)->willReturn('/api/v2/shop/product-variants/CODE');

        $this->normalize($product, null, [])->shouldReturn(['defaultVariant' => '/api/v2/shop/product-variants/CODE']);
    }

    function it_adds_default_variant_field_with_null_value_to_serialized_product_if_there_is_no_default_variant(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        NormalizerInterface $normalizer,
        ProductInterface $product
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($product, null, ['product_normalizer_already_called' => true])->willReturn([]);
        $defaultProductVariantResolver->getVariant($product)->willReturn(null);
        $iriConverter->getIriFromItem(Argument::any())->shouldNotBeCalled();

        $this->normalize($product, null, [])->shouldReturn(['defaultVariant' => null]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ProductInterface $product
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($product, null, ['product_normalizer_already_called' => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$product, null, ['product_normalizer_already_called' => true]])
        ;
    }
}
