<?php

namespace Sylius\Bundle\ApiBundle\DataProvider\Helpers;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Bundle\ApiBundle\Entity\Product\Product;

class ProductDataProviderHelper
{
    /**
     * @param Product $product
     * @return Product
     */
    public static function setCustomPropertiesToProduct($product)
    {
        $onHand = 0;

        foreach ($product->getVariants()->getValues() as $i => $variant) {
            if ($i === 0){
                $product->setPrice($variant->getChannelPricings()['FASHION_WEB']->getPrice());
            }

            $onHand += $variant->getOnHand();
        }
        $product->setOnHand($onHand);

        return $product;
    }
}
