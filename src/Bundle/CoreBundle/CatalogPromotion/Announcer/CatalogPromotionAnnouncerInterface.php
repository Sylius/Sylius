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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface CatalogPromotionAnnouncerInterface
{
    public function dispatchCatalogPromotionCreatedEvent(CatalogPromotionInterface $catalogPromotion): void;

    public function dispatchCatalogPromotionUpdatedEvent(CatalogPromotionInterface $catalogPromotion): void;
}
