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

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface ChannelPricingInterface extends ResourceInterface
{
    /**
     * @return ProductVariantInterface|null
     */
    public function getProductVariant(): ?ProductVariantInterface;

    /**
     * @param ProductVariantInterface|null $productVariant
     */
    public function setProductVariant(?ProductVariantInterface $productVariant): void;

    /**
     * @return int|null
     */
    public function getPrice(): ?int;

    /**
     * @param int|null $price
     */
    public function setPrice(?int $price): void;

    /**
     * @return string
     */
    public function getChannelCode(): ?string;

    /**
     * @param string|null $channelCode
     */
    public function setChannelCode(?string $channelCode): void;

    /**
     * @return int|null
     */
    public function getOriginalPrice(): ?int;

    /**
     * @param int|null $originalPrice
     */
    public function setOriginalPrice(?int $originalPrice): void;

    /**
     * @return bool
     */
    public function isPriceReduced(): bool;
}
