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
use Sylius\Bundle\CoreBundle\Provider\ForVariantsScopeVariantsProvider;
use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\CatalogPromotionVariantsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

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

    function it_provides_variants_configured_in_catalog_promotion_scopes(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionScopeInterface $firstScope,
        CatalogPromotionScopeInterface $secondScope,
        VariantsProviderInterface $firstProvider,
        VariantsProviderInterface $secondProvider,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
        ProductVariantInterface $thirdVariant
    ): void {
        $catalogPromotion->getScopes()->willReturn(new ArrayCollection([
            $firstScope->getWrappedObject(),
            $secondScope->getWrappedObject()
        ]));

        $firstScope->getType()->willReturn(ForVariantsScopeVariantsProvider::TYPE);
        $firstScope->getConfiguration()->willReturn(['variants' => ['PHP_T_SHIRT', 'PHP_MUG']]);

        $secondScope->getType()->willReturn(ForVariantsScopeVariantsProvider::TYPE);
        $secondScope->getConfiguration()->willReturn(['variants' => ['PHP_MUG', 'PHP_CAP']]);

        $firstProvider->supports($firstScope)->willReturn(false);
        $firstProvider->supports($secondScope)->willReturn(false);
        $firstProvider->provideEligibleVariants($firstScope)->shouldNotBeCalled();
        $firstProvider->provideEligibleVariants($secondScope)->shouldNotBeCalled();

        $secondProvider->supports($firstScope)->willReturn(true);
        $secondProvider->supports($secondScope)->willReturn(true);
        $secondProvider->provideEligibleVariants($firstScope)->willReturn([$firstVariant, $secondVariant]);
        $secondProvider->provideEligibleVariants($secondScope)->willReturn([$secondVariant, $thirdVariant]);

        $firstVariant->getCode()->willReturn('PHP_T_SHIRT');
        $secondVariant->getCode()->willReturn('PHP_MUG');
        $thirdVariant->getCode()->willReturn('PHP_CAP');

        $this
            ->provideEligibleVariants($catalogPromotion)
            ->shouldReturn([$firstVariant, $secondVariant, $thirdVariant])
        ;
    }
}
