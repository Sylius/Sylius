<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Provider;

use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface EligibleCatalogPromotionsProviderInterface
{
    /**
     * @param iterable|CriteriaInterface[] $criteria
     *
     * @return iterable|CatalogPromotionInterface[]
     */
    public function provide(iterable $criteria = []): iterable;
}
