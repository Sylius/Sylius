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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class ProductCreatedListener
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductCatalogPromotionsProcessorInterface $productCatalogPromotionsProcessor,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ProductCreated $event): void
    {
        /** @var ProductInterface|null $product */
        $product = $this->productRepository->findOneBy(['code' => $event->code]);
        if ($product === null) {
            return;
        }

        $this->productCatalogPromotionsProcessor->process($product);

        $this->entityManager->flush();
    }
}
