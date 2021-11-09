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
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class ForProductsScopeVariantsProviderSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);
    }

    function it_implements_variants_provider_interface(): void
    {
        $this->shouldImplement(VariantsProviderInterface::class);
    }

    function it_supports_only_for_products_catalog_promotion_scope(
        CatalogPromotionScopeInterface $forProductsScope,
        CatalogPromotionScopeInterface $forVariantsScope
    ): void {
        $forProductsScope->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_PRODUCTS);
        $forVariantsScope->getType()->willReturn(CatalogPromotionScopeInterface::TYPE_FOR_VARIANTS);

        $this->supports($forProductsScope)->shouldReturn(true);
        $this->supports($forVariantsScope)->shouldReturn(false);
    }

    function it_throws_an_exception_if_there_is_no_products_configured_in_the_scope_configuration(
        CatalogPromotionScopeInterface $catalogPromotionScope
    ): void {
        $catalogPromotionScope->getConfiguration()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('provideEligibleVariants', [$catalogPromotionScope])
        ;
    }

    function it_provides_variants_for_given_product_codes_if_they_exist(
        ProductRepositoryInterface $productRepository,
        CatalogPromotionScopeInterface $catalogPromotionScope,
        ProductInterface $mug,
        ProductInterface $tshirt,
        ProductVariantInterface $phpMug,
        ProductVariantInterface $javaMug,
        ProductVariantInterface $mTshirt
    ): void {
        $catalogPromotionScope->getConfiguration()->willReturn(['products' => ['MUG', 'CAP', 'T-SHIRT']]);

        $productRepository->findOneBy(['code' => 'MUG'])->willReturn($mug);
        $productRepository->findOneBy(['code' => 'T-SHIRT'])->willReturn($tshirt);
        $productRepository->findOneBy(['code' => 'CAP'])->willReturn(null);

        $mug->getVariants()->willReturn(new ArrayCollection([$phpMug->getWrappedObject(), $javaMug->getWrappedObject()]));
        $tshirt->getVariants()->willReturn(new ArrayCollection([$mTshirt->getWrappedObject()]));

        $this->provideEligibleVariants($catalogPromotionScope)->shouldReturn([$phpMug, $javaMug, $mTshirt]);
    }
}
