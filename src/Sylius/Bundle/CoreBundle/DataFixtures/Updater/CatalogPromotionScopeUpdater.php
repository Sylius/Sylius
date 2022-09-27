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

use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionScopeUpdater implements CatalogPromotionScopeUpdaterInterface
{
    public function update(CatalogPromotionScopeInterface $catalogPromotionScope, array $attributes): void
    {
        $catalogPromotionScope->setType($attributes['type']);
        $catalogPromotionScope->setConfiguration($attributes['configuration']);
    }
}
