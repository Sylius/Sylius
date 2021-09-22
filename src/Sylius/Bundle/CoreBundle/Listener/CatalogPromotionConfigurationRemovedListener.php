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

namespace Sylius\Bundle\CoreBundle\Listener;

use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionConfigurationRemoved;

final class CatalogPromotionConfigurationRemovedListener
{
    private CatalogPromotionClearerInterface $catalogPromotionClearer;

    public function __construct(
        CatalogPromotionClearerInterface $catalogPromotionClearer
    ) {
        $this->catalogPromotionClearer = $catalogPromotionClearer;
    }

    public function __invoke(CatalogPromotionConfigurationRemoved $event): void
    {
        $this->catalogPromotionClearer->clear();
    }
}
