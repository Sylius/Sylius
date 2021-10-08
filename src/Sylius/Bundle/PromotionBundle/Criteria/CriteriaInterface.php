<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface CriteriaInterface
{
    /**
     * @param array|CatalogPromotionInterface[] $catalogPromotions
     *
     * @return array|CatalogPromotionInterface[]
     */
    public function meets(array $catalogPromotions): array;
}
