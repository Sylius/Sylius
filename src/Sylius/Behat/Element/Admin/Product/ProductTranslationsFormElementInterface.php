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

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface ProductTranslationsFormElementInterface extends BaseFormElementInterface
{
    public function nameItIn(string $name, string $localeCode): void;

    public function hasNameInLocale(string $name, string $localeCode): bool;

    public function generateSlug(string $localeCode): void;

    public function getSlug(string $locale): string;

    public function specifySlugIn(string $slug, string $locale): void;

    public function setMetaKeywords(string $keywords, string $localeCode): void;

    public function setMetaDescription(string $description, string $localeCode): void;

    public function activateLanguageTab(string $localeCode): void;
}
