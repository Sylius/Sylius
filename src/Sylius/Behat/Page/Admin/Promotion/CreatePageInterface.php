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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function specifyLabel(string $label, string $localeCode): void;

    public function nameIt(string $name): void;

    public function addRule(?string $ruleName): void;

    public function selectRuleOption(string $option, string $value, bool $multiple = false): void;

    /**
     * @param string|string[] $value
     */
    public function selectAutocompleteRuleOption(string $option, array|string $value, bool $multiple = false): void;

    public function fillRuleOption(string $option, string $value): void;

    public function fillRuleOptionForChannel(string $channelCode, string $option, string $value): void;

    public function addAction(?string $actionName): void;

    public function selectActionOption(string $option, string $value, bool $multiple = false): void;

    public function fillActionOption(string $option, string $value): void;

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void;

    public function fillUsageLimit(string $limit): void;

    public function makeExclusive(): void;

    public function makeNotAppliesToDiscountedItem(): void;

    public function checkCouponBased(): void;

    public function checkChannel(string $name): void;

    public function setStartsAt(\DateTimeInterface $dateTime): void;

    public function setEndsAt(\DateTimeInterface $dateTime): void;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForAction(): string;

    /**
     * @param string|string[] $value
     */
    public function selectAutoCompleteFilterOption(string $option, array|string $value, bool $multiple = false): void;

    public function checkIfRuleConfigurationFormIsVisible(): bool;

    public function checkIfActionConfigurationFormIsVisible(): bool;

    public function hasLabel(string $label, string $localeCode): bool;
}
