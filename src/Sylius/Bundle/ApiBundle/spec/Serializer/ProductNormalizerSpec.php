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

use ApiPlatform\Api\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ProductNormalizerSpec extends ObjectBehavior
{
    function let(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($defaultProductVariantResolver, $iriConverter, $sectionProvider);

        $this->setNormalizer($normalizer);
    }

    function it_supports_only_product_interface_and_shop_api_section(
        SectionProviderInterface $sectionProvider,
        ProductInterface $product,
        OrderInterface $order,
    ): void {
        $this->supportsNormalization($order)->shouldReturn(false);

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this->supportsNormalization($product)->shouldReturn(true);

        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this->supportsNormalization($product)->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(ProductInterface $product): void
    {
        $this
            ->supportsNormalization($product, null, ['sylius_product_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_adds_default_variant_iri_to_serialized_product(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ProductInterface $product,
        ProductVariantInterface $variant,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, ['sylius_product_normalizer_already_called' => true])->willReturn([]);
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));
        $defaultProductVariantResolver->getVariant($product)->willReturn($variant);
        $iriConverter->getIriFromResource($variant)->willReturn('/api/v2/shop/product-variants/CODE');

        $this->normalize($product)->shouldReturn([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => '/api/v2/shop/product-variants/CODE',
        ]);
    }

    function it_adds_default_variant_field_with_null_value_to_serialized_product_if_there_is_no_default_variant(
        ProductVariantResolverInterface $defaultProductVariantResolver,
        IriConverterInterface $iriConverter,
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        ProductVariantInterface $variant,
        ProductInterface $product,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer->normalize($product, null, ['sylius_product_normalizer_already_called' => true])->willReturn([]);
        $iriConverter->getIriFromResource($variant)->willReturn('/api/v2/shop/product-variants/CODE');
        $product->getEnabledVariants()->willReturn(new ArrayCollection([$variant->getWrappedObject()]));

        $defaultProductVariantResolver->getVariant($product)->willReturn(null);

        $this->normalize($product)->shouldReturn([
            'variants' => ['/api/v2/shop/product-variants/CODE'],
            'defaultVariant' => null,
        ]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        ProductInterface $product,
    ): void {
        $normalizer->normalize($product, null, ['sylius_product_normalizer_already_called' => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$product, null, ['sylius_product_normalizer_already_called' => true]])
        ;
    }
}
