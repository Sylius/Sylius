<?php

namespace Sylius\Component\Core\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ChannelPricingInterface extends ResourceInterface, ChannelAwareInterface
{
    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant();

    /**
     * @param ProductVariantInterface|null $productVariant
     */
    public function setProductVariant(ProductVariantInterface $productVariant = null);

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param int $price
     */
    public function setPrice($price);
}
