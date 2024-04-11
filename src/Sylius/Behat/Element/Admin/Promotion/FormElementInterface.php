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

namespace Sylius\Behat\Element\Admin\Promotion;

interface FormElementInterface
{
    public function setPriority(?int $priority): void;

    public function getPriority(): int;

    public function setStartsAt(\DateTimeInterface $dateTime): void;

    public function setEndsAt(\DateTimeInterface $dateTime): void;

    public function setUsageLimit(int $limit): void;

    public function makeExclusive(): void;

    public function makeNotAppliesToDiscountedItem(): void;

    public function makeCouponBased(): void;

    public function checkChannel(string $name): void;

    public function setLabel(string $label, string $localeCode): void;

    public function hasLabel(string $label, string $localeCode): bool;

    public function addAction(?string $actionName): void;

    public function fillActionOption(string $option, string $value): void;

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void;

    public function selectActionOption(string $option, string $value, bool $multiple = false): void;

    public function addRule(?string $ruleName): void;

    public function selectRuleOption(string $option, string $value, bool $multiple = false): void;

    public function fillRuleOption(string $option, string $value): void;

    public function fillRuleOptionForChannel(string $channelCode, string $option, string $value): void;

    public function selectAutocompleteRuleOptions(array $values, ?string $channelCode = null): void;

    public function selectAutocompleteFilterOptions(array $values, string $channelCode, string $filterType): void;

    public function checkIfRuleConfigurationFormIsVisible(): bool;

    public function checkIfActionConfigurationFormIsVisible(): bool;

    public function getValidationMessage(string $element): string;

    public function getValidationMessageForAction(): string;

    public function getValidationMessageForTranslation(string $element, string $localeCode): string;
}
