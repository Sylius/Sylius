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

namespace spec\Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Processor\AllCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantCreatedListenerSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        AllCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith($productVariantRepository, $allCatalogPromotionsProcessor, $entityManager);
    }

    function it_processes_catalog_promotions_for_created_product_variant(
        ProductVariantRepositoryInterface $productVariantRepository,
        AllCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
        ProductVariantInterface $variant
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn($variant);

        $allCatalogPromotionsProcessor->process()->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this(new ProductVariantCreated('PHP_MUG'));
    }

    function it_does_nothing_if_there_is_no_product_variant_with_given_code(
        ProductVariantRepositoryInterface $productVariantRepository,
        AllCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn(null);

        $allCatalogPromotionsProcessor->process()->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new ProductVariantCreated('PHP_MUG'));
    }
}
