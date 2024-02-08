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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ProductVariantCatalogPromotionsProcessor implements ProductVariantCatalogPromotionsProcessorInterface
{
    public function __construct(
        private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher,
    ) {
    }

    public function process(ProductVariantInterface $variant): void
    {
        $this->commandDispatcher->updateVariants([$variant->getCode()]);
    }
}
