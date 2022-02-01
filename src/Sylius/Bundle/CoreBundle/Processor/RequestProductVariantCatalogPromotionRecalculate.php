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

use Sylius\Bundle\CoreBundle\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class RequestProductVariantCatalogPromotionRecalculate implements RequestProductVariantCatalogPromotionRecalculateInterface
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher
    ) {
    }

    public function recalculate(): void
    {
        $variantsCodes = $this->productVariantRepository->getCodesOfAllVariants();

        $this->commandDispatcher->updateVariants($variantsCodes);
    }
}
