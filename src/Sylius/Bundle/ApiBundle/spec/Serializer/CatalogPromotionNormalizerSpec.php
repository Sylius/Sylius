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
use ApiPlatform\Core\Exception\ItemNotFoundException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CatalogPromotionNormalizerSpec extends ObjectBehavior
{
    function let(IriConverterInterface $iriConverter, ProductVariantRepository $productVariantRepository): void
    {
        $this->beConstructedWith($iriConverter, $productVariantRepository);
    }

    function it_supports_normalization_only_for_catalog_promotion(CatalogPromotionInterface $catalogPromotion, ResourceInterface $resource): void
    {
        $this->supportsNormalization($catalogPromotion)->shouldReturn(true);
        $this->supportsNormalization($resource)->shouldReturn(false);
    }

    function it_does_not_support_normalization_if_was_called_before(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->supportsNormalization($catalogPromotion, null, ['sylius_catalog_promotion_normalizer_already_called' => true])->shouldReturn(false);
    }

    function it_returns_iri_instead_of_codes(
        CatalogPromotionInterface $catalogPromotion,
        NormalizerInterface $normalizer,
        ProductVariantRepository $productVariantRepository,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        IriConverterInterface $iriConverter
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($catalogPromotion, null, ['sylius_catalog_promotion_normalizer_already_called' => true])->willReturn(
            [
                'rules' => [
                    [
                        'configuration' => [
                            'MUG',
                            'MUG_2'
                        ]
                    ]
                ]
            ]
        );

        $productVariantRepository->findOneBy(['code' => 'MUG'])->willReturn($productVariantOne);
        $productVariantRepository->findOneBy(['code' => 'MUG_2'])->willReturn($productVariantTwo);

        $iriConverter->getIriFromItem($productVariantOne)->willReturn('api/v2/admin/product-variants/MUG');
        $iriConverter->getIriFromItem($productVariantTwo)->willReturn('api/v2/admin/product-variants/MUG_2');

        $this->normalize($catalogPromotion, null, [])->shouldReturn(
            [
                'rules' => [
                    [
                        'configuration' => [
                            'api/v2/admin/product-variants/MUG',
                            'api/v2/admin/product-variants/MUG_2'
                        ]
                    ]
                ]
            ]
        );
    }

    function it_throws_item_not_found_exception_when_product_with_code_does_not_exist(
        CatalogPromotionInterface $catalogPromotion,
        NormalizerInterface $normalizer,
        ProductVariantRepository $productVariantRepository,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo,
        IriConverterInterface $iriConverter
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($catalogPromotion, null, ['sylius_catalog_promotion_normalizer_already_called' => true])->willReturn(
            [
                'rules' => [
                    [
                        'configuration' => [
                            'MUG',
                            'MUG_2'
                        ]
                    ]
                ]
            ]
        );

        $productVariantRepository->findOneBy(['code' => 'MUG'])->willReturn(null);
        $productVariantRepository->findOneBy(['code' => 'MUG_2'])->willReturn($productVariantTwo);


        $iriConverter->getIriFromItem($productVariantOne)->shouldNotBeCalled();
        $iriConverter->getIriFromItem($productVariantTwo)->shouldNotBeCalled();

        $this->shouldThrow(ItemNotFoundException::class)->during('normalize', [$catalogPromotion, null, []]);
    }
}
