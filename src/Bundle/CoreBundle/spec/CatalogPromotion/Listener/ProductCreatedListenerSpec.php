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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class ProductCreatedListenerSpec extends ObjectBehavior
{
    function let(
        ProductRepositoryInterface $productRepository,
        ProductCatalogPromotionsProcessorInterface $productCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
    ): void {
        $this->beConstructedWith($productRepository, $productCatalogPromotionsProcessor, $entityManager);
    }

    function it_processes_catalog_promotions_for_created_product(
        ProductRepositoryInterface $productRepository,
        ProductCatalogPromotionsProcessorInterface $productCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
        ProductInterface $product,
    ): void {
        $productRepository->findOneBy(['code' => 'MUG'])->willReturn($product);

        $productCatalogPromotionsProcessor->process($product)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this(new ProductCreated('MUG'));
    }

    function it_does_nothing_if_there_is_no_product_with_given_code(
        ProductRepositoryInterface $productRepository,
        ProductCatalogPromotionsProcessorInterface $productCatalogPromotionsProcessor,
        EntityManagerInterface $entityManager,
    ): void {
        $productRepository->findOneBy(['code' => 'MUG'])->willReturn(null);

        $productCatalogPromotionsProcessor->process(Argument::any())->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this(new ProductCreated('MUG'));
    }
}
