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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class CatalogPromotionDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        IriConverterInterface $iriConverter
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $iriConverter);
    }

    function it_supports_only_catalog_promotion_entity(CatalogPromotionInterface $catalogPromotion, ResourceInterface $resource): void
    {
        $this->supports($catalogPromotion)->shouldReturn(true);
        $this->supports($resource)->shouldReturn(false);
    }

    function it_replaces_codes_with_iri_in_configuration(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        IriConverterInterface $iriConverter,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionRuleInterface $catalogPromotionRuleOne,
        CatalogPromotionRuleInterface $catalogPromotionRuleTwo,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo
    ): void {
        $catalogPromotion->getRules()->willReturn(new ArrayCollection([$catalogPromotionRuleOne->getWrappedObject(), $catalogPromotionRuleTwo->getWrappedObject()]));

        $catalogPromotionRuleOne->getConfiguration()->willReturn(['api/v2/admin/product-variants/MUG']);
        $catalogPromotionRuleTwo->getConfiguration()->willReturn(['api/v2/admin/product-variants/MUG_2']);

        $iriConverter->getItemFromIri('api/v2/admin/product-variants/MUG')->willReturn($productVariantOne);
        $iriConverter->getItemFromIri('api/v2/admin/product-variants/MUG_2')->willReturn($productVariantTwo);

        $productVariantOne->getCode()->willReturn('MUG');
        $productVariantTwo->getCode()->willReturn('MUG_2');

        $catalogPromotionRuleOne->setConfiguration(['MUG'])->shouldBeCalled();
        $catalogPromotionRuleTwo->setConfiguration(['MUG_2'])->shouldBeCalled();

        $decoratedDataPersister->persist($catalogPromotion, [])->shouldBeCalled();

        $this->persist($catalogPromotion, []);
    }

    function it_throws_item_not_found_exception_if_product_variant_is_not_found(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        IriConverterInterface $iriConverter,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionRuleInterface $catalogPromotionRuleOne,
        CatalogPromotionRuleInterface $catalogPromotionRuleTwo,
        ProductVariantInterface $productVariantOne,
        ProductVariantInterface $productVariantTwo
    ): void {
        $catalogPromotion->getRules()->willReturn(new ArrayCollection([$catalogPromotionRuleOne->getWrappedObject(), $catalogPromotionRuleTwo->getWrappedObject()]));

        $catalogPromotionRuleOne->getConfiguration()->willReturn(['api/v2/admin/product-variants/MUG']);
        $catalogPromotionRuleTwo->getConfiguration()->willReturn(['api/v2/admin/product-variants/MUG_2']);

        $iriConverter->getItemFromIri('api/v2/admin/product-variants/MUG')->willReturn(null);
        $iriConverter->getItemFromIri('api/v2/admin/product-variants/MUG_2')->willReturn($productVariantTwo);

        $productVariantOne->getCode()->shouldNotBeCalled();
        $productVariantTwo->getCode()->shouldNotBeCalled();

        $catalogPromotionRuleOne->setConfiguration(['MUG'])->shouldNotBeCalled();
        $catalogPromotionRuleTwo->setConfiguration(['MUG_2'])->shouldNotBeCalled();

        $decoratedDataPersister->persist($catalogPromotion, [])->shouldNotBeCalled();

        $this->shouldThrow(ItemNotFoundException::class)->during('persist', [$catalogPromotion, []]);
    }

    function it_removes_catalog_promotion(ContextAwareDataPersisterInterface $decoratedDataPersister, CatalogPromotionInterface $catalogPromotion): void
    {
        $decoratedDataPersister->remove($catalogPromotion, [])->shouldBeCalled();

        $this->remove($catalogPromotion, []);
    }
}
