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
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function setPriority(?int $priority): void;

    public function getPriority(): int;

    public function nameIt(string $name): void;

    public function checkChannelsState(string $channelName): bool;

    public function isCodeDisabled(): bool;

    public function fillUsageLimit(string $limit): void;

    public function makeExclusive(): void;

    public function checkCouponBased(): void;

    public function checkChannel(string $name): void;

    public function setStartsAt(\DateTimeInterface $dateTime): void;

    public function setEndsAt(\DateTimeInterface $dateTime): void;

    public function hasStartsAt(\DateTimeInterface $dateTime): bool;

    public function hasEndsAt(\DateTimeInterface $dateTime): bool;

    public function isCouponManagementAvailable(): bool;

    public function manageCoupons(): void;

    public function hasAnyRule(): bool;

    public function hasRule(string $name): bool;

    public function removeActionFieldValue(string $channelCode, string $field): void;

    public function getItemPercentageDiscountActionValue(string $channelCode): string;

    public function specifyOrderPercentageDiscountActionValue(string $discount): void;

    public function getOrderPercentageDiscountActionValue(): string;

    public function removeRuleAmount(string $channelCode): void;

    public function getActionValidationErrorsCount(string $channelCode): int;

    public function getRuleValidationErrorsCount(string $channelCode): int;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationMessageForTranslation(string $element, string $localeCode): string;
}
