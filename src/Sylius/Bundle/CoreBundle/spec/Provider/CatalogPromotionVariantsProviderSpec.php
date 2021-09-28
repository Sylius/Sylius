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

    function it_provides_variants_configured_in_catalog_promotion_rule(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionRuleInterface $rule,
        VariantsProviderInterface $firstProvider,
        VariantsProviderInterface $secondProvider,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $catalogPromotion->getRules()->willReturn(new ArrayCollection([$rule->getWrappedObject()]));
        $rule->getConfiguration()->willReturn(['variants' => ['PHP_T_SHIRT_XS_WHITE', 'PHP_T_SHIRT_XS_BLACK', 'PHP_MUG']]);
        $rule->getType()->willReturn(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);

        $firstProvider->supports($rule)->willReturn(false);
        $firstProvider->provideEligibleVariants($rule)->shouldNotBeCalled();
        $secondProvider->supports($rule)->willReturn(true);
        $secondProvider->provideEligibleVariants($rule)->willReturn([$firstVariant, $secondVariant]);

        $this
            ->provideEligibleVariants($catalogPromotion)
            ->shouldReturn([$firstVariant, $secondVariant])
        ;
    }
}
