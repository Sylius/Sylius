<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Formatter;

use Sylius\Component\Core\Model\CatalogPromotionInterface;

interface AppliedPromotionInformationFormatterInterface
{
    public function format(CatalogPromotionInterface $catalogPromotion): CatalogPromotionInterface;
}
