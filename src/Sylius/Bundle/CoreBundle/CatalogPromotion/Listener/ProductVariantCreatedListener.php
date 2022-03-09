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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantCreatedListener
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ProductVariantCreated $event): void
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantRepository->findOneBy(['code' => $event->code]);
        if ($variant === null) {
            return;
        }

        $this->productVariantCatalogPromotionsProcessor->process($variant);

        $this->entityManager->flush();
    }
}
