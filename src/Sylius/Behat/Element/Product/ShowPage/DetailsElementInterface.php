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

namespace Sylius\Behat\Element\Product\ShowPage;

interface DetailsElementInterface
{
    public function getProductCode(): string;

    public function hasChannel(string $channelName): bool;

    public function countChannels(): int;

    public function getProductCurrentStock(): int;

    public function getProductTaxCategory(): string;
}
