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

namespace Sylius\Behat\Element\Admin\CatalogPromotion;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function nameIt(string $name): void;

    public function labelIt(string $label, string $localeCode): void;

    public function describeIt(string $description, string $localeCode): void;

    public function prioritizeIt(int $priority): void;

    public function changeEnableTo(bool $enabled): void;

    public function checkChannel(string $channelName): void;

    public function setExclusiveness(bool $isExclusive): void;

    public function uncheckChannel(string $channelName): void;

    public function specifyStartDate(\DateTimeInterface $startDate): void;

    public function specifyEndDate(\DateTimeInterface $endDate): void;

    public function addScope(string $type): void;

    public function addAction(string $type): void;

    public function selectScopeOption(array $values): void;

    public function fillActionOption(string $option, string $value): void;

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void;

    public function getLastScopeCodes(): array;

    public function getLastActionOption(string $option): string;

    public function getLastActionOptionForChannel(string $channelCode, string $option): string;

    public function getFieldValueInLocale(string $field, string $localeCode): string;

    public function checkIfScopeConfigurationFormIsVisible(): bool;

    public function checkIfActionConfigurationFormIsVisible(): bool;

    public function getValidationMessages(): array;

    public function removeScopeOption(array $values): void;

    public function removeLastAction(): void;

    public function removeLastScope(): void;
}
