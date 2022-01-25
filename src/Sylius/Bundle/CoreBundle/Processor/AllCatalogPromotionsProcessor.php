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

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Bundle\CoreBundle\Announcer\BatchedVariantsUpdateAnnouncerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    public function __construct(
        private CatalogPromotionClearerInterface $catalogPromotionClearer,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private BatchedVariantsUpdateAnnouncerInterface $announcer
    ) {
    }

    public function process(): void
    {
        $this->catalogPromotionClearer->clear();

        $variantsCodes = $this->productVariantRepository->getCodesOfAllVariants();

        $this->announcer->dispatchVariantsUpdateCommand($variantsCodes);
    }
}
