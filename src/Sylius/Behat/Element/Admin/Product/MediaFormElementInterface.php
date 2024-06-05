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

namespace Sylius\Behat\Element\Admin\Product;

use Sylius\Component\Core\Model\ProductVariantInterface;

interface MediaFormElementInterface
{
    public function attachImage(string $path, ?string $type = null, ?ProductVariantInterface $productVariant = null): void;

    public function hasLastImageAVariant(ProductVariantInterface $productVariant): bool;

    public function changeImageWithType(string $type, string $path): void;

    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    public function hasImageWithType(string $type): bool;

    public function hasImageWithVariant(ProductVariantInterface $productVariant): bool;

    public function countImages(): int;

    public function modifyFirstImageType(string $type): void;

    public function selectVariantForFirstImage(ProductVariantInterface $productVariant): void;

    public function isImageWithTypeDisplayed(string $type): bool;
}
