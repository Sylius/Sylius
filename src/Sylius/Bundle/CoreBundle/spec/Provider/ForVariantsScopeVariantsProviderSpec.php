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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

final class ForVariantsScopeVariantsProviderSpec extends ObjectBehavior
{
    function let(ProductVariantRepositoryInterface $productVariantRepository): void
    {
        $this->beConstructedWith($productVariantRepository);
    }

    function it_implements_catalog_promotion_products_provider_interface(): void
    {
        $this->shouldImplement(VariantsProviderInterface::class);
    }

    function it_provides_eligible_variants_for_variants_base_catalog_promotion_scope(
        ProductVariantRepositoryInterface $productVariantRepository,
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $scope->getConfiguration()->willReturn(['variants' => ['PHP_T_SHIRT_XS_WHITE', 'PHP_T_SHIRT_XS_BLACK', 'PHP_MUG']]);

        $productVariantRepository->findOneBy(['code' => 'PHP_T_SHIRT_XS_WHITE'])->willReturn($firstVariant);
        $productVariantRepository->findOneBy(['code' => 'PHP_T_SHIRT_XS_BLACK'])->willReturn($secondVariant);
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn(null);

        $this
            ->provideEligibleVariants($scope)
            ->shouldReturn([$firstVariant, $secondVariant])
        ;
    }
}
