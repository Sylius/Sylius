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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantUpdatedListenerSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
    ): void {
        $this->beConstructedWith($productVariantRepository, $productVariantCatalogPromotionsProcessor, $entityManager);
    }

    function it_processes_catalog_promotions_for_updated_product_variant(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
        ProductVariantInterface $variant,
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn($variant);

        $productVariantCatalogPromotionsProcessor->process($variant)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this(new ProductVariantUpdated('PHP_MUG'));
    }

    function it_does_nothing_if_there_is_no_product_variant_with_given_code(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn(null);

        $productVariantCatalogPromotionsProcessor->process(Argument::any())->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new ProductVariantUpdated('PHP_MUG'));
    }
}
