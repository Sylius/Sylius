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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\TaxonInterface;

interface CreateConfigurableProductPageInterface extends BaseCreatePageInterface
{
    public function selectOption(string $optionName): void;

    public function specifyCode(string $code): void;

    public function nameItIn(string $name, string $localeCode): void;

    public function hasMainTaxonWithName(string $taxonName): bool;

    public function selectMainTaxon(TaxonInterface $taxon): void;

    public function attachImage(string $path, ?string $type = null): void;

    public function activateLanguageTab(string $localeCode): void;

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;
}
