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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\Api\IriConverterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\Promotion;
use Sylius\Component\Promotion\DTO\CatalogPromotion as CatalogPromotionDTO;
use Sylius\Component\Promotion\Factory\CatalogPromotionRuleFactoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CatalogPromotionDataTransformerSpec extends ObjectBehavior
{
    function let(
        IriConverterInterface $iriConverter,
        CatalogPromotionRuleFactoryInterface $catalogPromotionRuleFactory,
        FactoryInterface $catalogPromotionFactory): void
    {
        $this->beConstructedWith($iriConverter, $catalogPromotionRuleFactory, $catalogPromotionFactory);
    }

    function it_supports_only_catalog_promotion(
    ): void {
        $this->supportsTransformation([], CatalogPromotion::class)->shouldReturn(true);
        $this->supportsTransformation([], Promotion::class)->shouldReturn(false);
    }

    function it_transforms_dto_to_catalog_promotion_entity(
        FactoryInterface $catalogPromotionFactory,
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        IriConverterInterface $iriConverter,
        CatalogPromotionRuleFactoryInterface $catalogPromotionRuleFactory,
        CatalogPromotionRuleInterface $catalogPromotionRule
    ): void {
        $promotionRules = [
            [
                'type' => CatalogPromotionRuleInterface::CATALOG_PROMOTION_RULE_CONTAINS_VARIANTS_TYPE,
                'configuration' => [
                    'api/firstVariant/iri',
                    'api/secondVariant/iri',
                ]
            ]
        ];

        $object = new CatalogPromotionDTO('Winter sales', 'winter_sales', $promotionRules);

        $catalogPromotionFactory->createNew()->willReturn($catalogPromotion);
        $catalogPromotion->setName('Winter sales')->shouldBeCalled();
        $catalogPromotion->setCode('winter_sales')->shouldBeCalled();

        $iriConverter->getItemFromIri('api/firstVariant/iri')->willReturn($firstVariant);
        $firstVariant->getCode()->willReturn('first_variant_code');

        $iriConverter->getItemFromIri('api/secondVariant/iri')->willReturn($secondVariant);
        $secondVariant->getCode()->willReturn('second_variant_code');

        $catalogPromotionRuleFactory->createWithData(
            CatalogPromotionRuleInterface::CATALOG_PROMOTION_RULE_CONTAINS_VARIANTS_TYPE,
            $catalogPromotion,
            ['first_variant_code', 'second_variant_code']
        )
        ->willReturn($catalogPromotionRule);

        $catalogPromotion->addRule($catalogPromotionRule)->shouldBeCalled();

        $this->transform($object, CatalogPromotion::class, [])->shouldReturn($catalogPromotion);
    }
}
