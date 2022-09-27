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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionActionUpdater implements CatalogPromotionActionUpdaterInterface
{
    public function update(CatalogPromotionActionInterface $catalogPromotionAction, array $attributes): void
    {
        $catalogPromotionAction->setType($attributes['type']);
        $catalogPromotionAction->setConfiguration($attributes['configuration']);
    }
}
