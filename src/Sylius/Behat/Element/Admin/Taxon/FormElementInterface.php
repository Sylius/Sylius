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

namespace Sylius\Behat\Element\Admin\Taxon;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function getCode(): string;

    public function nameIt(string $name, string $localeCode): void;

    public function slugIt(string $slug, string $localeCode): void;

    public function attachImage(string $path, ?string $type = null): void;

    public function changeImageWithType(string $type, string $path): void;

    public function modifyFirstImageType(string $type): void;

    public function removeImageWithType(string $type): void;

    public function removeFirstImage(): void;

    public function isImageWithTypeDisplayed(string $type): bool;

    public function countImages(): int;
}
