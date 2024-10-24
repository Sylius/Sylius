<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\CatalogPromotion;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ShowPageInterface extends PageInterface
{
    public function getName(): string;

    public function getStartDate(): string;

    public function getEndDate(): string;

    public function getPriority(): int;

    public function hasActionWithPercentageDiscount(string $amount): bool;

    public function hasActionWithFixedDiscount(string $amount, ChannelInterface $channel): bool;

    public function hasScopeWithVariant(ProductVariantInterface $variant): bool;

    public function hasScopeWithProduct(ProductInterface $product): bool;

    public function isExclusive(): bool;
}
