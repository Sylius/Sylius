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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\EditToShowPageSwitcherInterface;
use Sylius\Behat\Page\Admin\ShowPageButtonCheckerInterface;

interface UpdateConfigurableProductPageInterface extends
    UpdatePageInterface,
    EditToShowPageSwitcherInterface,
    ShowPageButtonCheckerInterface
{
    public function isCodeDisabled(): bool;

    public function isProductOptionChosen(string $option): bool;

    public function isProductOptionsDisabled(): bool;

    public function checkChannel(string $channelCode): void;

    public function goToVariantsList(): void;

    public function goToVariantCreation(): void;

    public function goToVariantGeneration(): void;

    public function hasTab(string $name): bool;
}
