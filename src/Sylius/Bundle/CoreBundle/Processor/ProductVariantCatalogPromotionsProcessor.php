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
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessor implements ProductVariantCatalogPromotionsProcessorInterface
{
    public function __construct(
        private CatalogPromotionClearerInterface $catalogPromotionClearer,
        private BatchedVariantsUpdateAnnouncerInterface $announcer
    ) {
    }

    public function process(ProductVariantInterface $variant): void
    {
        $this->catalogPromotionClearer->clearVariant($variant);

        $this->announcer->dispatchVariantsUpdateCommand([$variant->getCode()]);
    }
}
