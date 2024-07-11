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

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Behat\Page\Admin\EditToShowPageSwitcherInterface;
use Sylius\Behat\Page\Admin\ShowPageButtonCheckerInterface;

interface UpdateSimpleProductPageInterface extends
    BaseUpdatePageInterface,
    EditToShowPageSwitcherInterface,
    ShowPageButtonCheckerInterface
{
    public function isCodeDisabled(): bool;

    public function disableTracking(): void;

    public function enableTracking(): void;

    public function isTracked(): bool;

    public function isShippingRequired(): bool;

    public function goToVariantsList(): void;

    public function goToVariantCreation(): void;

    public function goToVariantGeneration(): void;

    public function hasTab(string $name): bool;

    public function getShowProductInSingleChannelUrl(): string;

    public function isShowInShopButtonDisabled(): bool;

    public function showProductInChannel(string $channel): void;

    public function showProductInSingleChannel(): void;

    public function disable(): void;

    public function isEnabled(): bool;

    public function enable(): void;
}
