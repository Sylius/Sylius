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

use Sylius\Bundle\CoreBundle\Commander\UpdateVariantsCommanderInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class AllCatalogPromotionsProcessor implements AllCatalogPromotionsProcessorInterface
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private UpdateVariantsCommanderInterface $commander
    ) {
    }

    public function process(): void
    {
        $variantsCodes = $this->productVariantRepository->getCodesOfAllVariants();

        $this->commander->updateVariants($variantsCodes);
    }
}
