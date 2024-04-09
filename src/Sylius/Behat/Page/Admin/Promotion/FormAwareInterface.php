<?php

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

interface FormAwareInterface
{
    public function setStartsAt(\DateTimeInterface $dateTime): void;

    public function setEndsAt(\DateTimeInterface $dateTime): void;

    public function setUsageLimit(int $limit): void;

    public function makeExclusive(): void;

    public function makeNotAppliesToDiscountedItem(): void;

    public function makeCouponBased(): void;

    public function checkChannel(string $name): void;
}
