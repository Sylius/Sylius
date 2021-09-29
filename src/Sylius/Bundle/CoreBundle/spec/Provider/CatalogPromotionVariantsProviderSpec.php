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

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;

final class CatalogPromotionVariantsProviderSpec extends ObjectBehavior
{
    function let(VariantsProviderInterface $firstProvider, VariantsProviderInterface $secondProvider): void
    {
        $this->beConstructedWith([$firstProvider, $secondProvider]);
    }

    function it_implements_catalog_promotion_products_provider_interface(): void
    {
        $this->shouldImplement(CatalogPromotionVariantsProviderInterface::class);
    }

    function it_provides_variants_configured_in_catalog_promotion_rules(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionRuleInterface $firstRule,
        CatalogPromotionRuleInterface $secondRule,
        VariantsProviderInterface $firstProvider,
        VariantsProviderInterface $secondProvider,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        ProductVariantInterface $thirdVariant
    ): void {
        $catalogPromotion->getRules()->willReturn(new ArrayCollection([
            $firstRule->getWrappedObject(),
            $secondRule->getWrappedObject()
        ]));

        $firstRule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $firstRule->getConfiguration()->willReturn(['variants' => ['PHP_T_SHIRT', 'PHP_MUG']]);

        $secondRule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $secondRule->getConfiguration()->willReturn(['variants' => ['PHP_MUG', 'PHP_CAP']]);

        $firstProvider->supports($firstRule)->willReturn(false);
        $firstProvider->supports($secondRule)->willReturn(false);
        $firstProvider->provideEligibleVariants($firstRule)->shouldNotBeCalled();
        $firstProvider->provideEligibleVariants($secondRule)->shouldNotBeCalled();

        $secondProvider->supports($firstRule)->willReturn(true);
        $secondProvider->supports($secondRule)->willReturn(true);
        $secondProvider->provideEligibleVariants($firstRule)->willReturn([$firstVariant, $secondVariant]);
        $secondProvider->provideEligibleVariants($secondRule)->willReturn([$secondVariant, $thirdVariant]);

        $firstVariant->getCode()->willReturn('PHP_T_SHIRT');
        $secondVariant->getCode()->willReturn('PHP_MUG');
        $thirdVariant->getCode()->willReturn('PHP_CAP');

        $this
            ->provideEligibleVariants($catalogPromotion)
            ->shouldReturn([$firstVariant, $secondVariant, $thirdVariant])
        ;
    }
}
