<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Applicator;

use Sylius\Component\Core\Model\ProductInterface;

interface ProductCatalogPromotionApplicatorInterface
{
    public function applyPercentageDiscount(ProductInterface $product, float $discount): void;
}
