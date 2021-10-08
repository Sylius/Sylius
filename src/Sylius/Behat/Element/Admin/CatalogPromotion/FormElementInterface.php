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

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

interface FormElementInterface
{
    public function nameIt(string $name): void;

    public function labelIt(string $label, string $localeCode): void;

    public function describeIt(string $description, string $localeCode): void;

    public function changeEnableTo(bool $enabled): void;

    public function checkChannel(string $channelName): void;

    public function uncheckChannel(string $channelName): void;

    public function addScope(): void;

    public function addAction(): void;

    public function chooseScopeType(string $type): void;

    public function chooseLastScopeVariants(array $variantCodes): void;

    public function chooseLastScopeTaxons(array $taxonsCodes): void;

    public function specifyLastActionDiscount(string $discount): void;

    public function getFieldValueInLocale(string $field, string $localeCode): string;

    public function getLastScopeVariantCodes(): array;

    public function getLastScopeTaxonsCodes(): array;

    public function getLastActionDiscount(): string;

    public function getValidationMessageForAction(): string;

    public function removeAllActions(): void;

    public function removeAllScopes(): void;
}
