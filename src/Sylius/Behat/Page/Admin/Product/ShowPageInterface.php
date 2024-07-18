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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Page\Admin\ShowToEditPageSwitcherInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

interface ShowPageInterface extends SymfonyPageInterface, ShowToEditPageSwitcherInterface
{
    /** @return string[] */
    public function getAppliedCatalogPromotionsLinks(string $variantName, string $channelName): array;

    /** @return string[] */
    public function getAppliedCatalogPromotionsNames(string $variantName, string $channelName): array;

    public function getName(): string;

    public function getBreadcrumb(): string;

    public function isSimpleProductPage(): bool;

    public function isShowInShopButtonDisabled(): bool;

    public function showProductInChannel(string $channel): void;

    public function showProductInSingleChannel(): void;

    public function showVariantEditPage(ProductVariantInterface $variant): void;
}
