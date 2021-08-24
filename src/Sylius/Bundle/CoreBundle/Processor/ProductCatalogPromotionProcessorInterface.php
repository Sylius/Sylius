<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface ProductCatalogPromotionProcessorInterface
{
    public function process(CatalogPromotionInterface $catalogPromotion): void;
}
