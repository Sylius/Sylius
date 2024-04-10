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
    public function prioritizeIt(?int $priority): void;

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
}
