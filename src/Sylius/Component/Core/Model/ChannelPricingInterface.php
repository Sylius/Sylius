<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Channel\Model\ChannelAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface ChannelPricingInterface extends ResourceInterface
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

    /**
     * @return string
     */
    public function getChannelCode();

    /**
     * @param string $channelCode
     */
    public function setChannelCode($channelCode);

    /**
     * @return int
     */
    public function getOriginalPrice();

    /**
     * @param int $originalPrice
     */
    public function setOriginalPrice($originalPrice);

    /**
     * @return bool
     */
    public function isPriceReduced();
}
