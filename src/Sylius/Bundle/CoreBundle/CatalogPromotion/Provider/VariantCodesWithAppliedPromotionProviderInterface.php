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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Provider;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface VariantCodesWithAppliedPromotionProviderInterface
{
    /**
     * Returns the codes of the variants having the given promotion among the applied promotions
     * of at least one of their channel pricings, whatever the channel.
     *
     * @return list<string>
     */
    public function provide(CatalogPromotionInterface $catalogPromotion): array;
}
