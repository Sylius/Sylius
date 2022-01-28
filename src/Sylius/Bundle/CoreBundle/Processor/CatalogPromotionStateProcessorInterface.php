<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Processor;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface CatalogPromotionStateProcessorInterface
{
    public function process(CatalogPromotionInterface $catalogPromotion): void;
}
