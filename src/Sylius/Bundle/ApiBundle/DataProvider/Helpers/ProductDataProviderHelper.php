<?php

namespace Sylius\Bundle\ApiBundle\DataProvider\Helpers;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Bundle\ApiBundle\Entity\Product\Product;

class ProductDataProviderHelper
{
    /**
     * @param Product $product
     * @param string $channel
     * @return Product
     */
    public static function setCustomPropertiesToProduct($product, $channel)
    {
        $onHand = 0;

        foreach ($product->getVariants()->getValues() as $i => $variant) {
            if ($i === 0){
                $product->setPrice($variant->getChannelPricings()[$channel]->getPrice());
            }

            $onHand += $variant->getOnHand();
        }
        $product->setOnHand($onHand);

        return $product;
    }
}
