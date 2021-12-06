<?php

declare(strict_types=1);

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface CatalogPromotionVariantsProviderInterface
{
    public function provideEligibleVariants(CatalogPromotionInterface $catalogPromotion): array;
}
