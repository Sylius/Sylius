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

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface TargetedCatalogPromotionProcessorInterface
{
    /**
     * Recalculates only the variants that may be affected by the given catalog promotion:
     * the ones it is currently applied on and the ones in the current scope of the promotion.
     */
    public function process(CatalogPromotionInterface $catalogPromotion): void;
}
