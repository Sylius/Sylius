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
use Sylius\Bundle\CoreBundle\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantUpdateListenerSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $catalogPromotionsProcessor,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith($productVariantRepository, $catalogPromotionsProcessor, $entityManager);
    }

    function it_processes_catalog_promotions_for_updated_product_variant(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $catalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
        ProductVariantInterface $variant
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn($variant);

        $catalogPromotionsProcessor->process($variant)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this(new ProductVariantUpdated('PHP_MUG'));
    }

    function it_does_nothing_if_there_is_no_product_variant_with_given_code(
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantCatalogPromotionsProcessorInterface $catalogPromotionsProcessor,
        EntityManagerInterface $entityManager
    ): void {
        $productVariantRepository->findOneBy(['code' => 'PHP_MUG'])->willReturn(null);

        $catalogPromotionsProcessor->process(Argument::any())->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new ProductVariantUpdated('PHP_MUG'));
    }
}
