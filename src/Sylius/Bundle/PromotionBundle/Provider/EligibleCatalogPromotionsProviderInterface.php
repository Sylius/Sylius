<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface EligibleCatalogPromotionsProviderInterface
{
    /**
     * @return array|CatalogPromotionInterface[]
     */
    public function provide(): array;
}
