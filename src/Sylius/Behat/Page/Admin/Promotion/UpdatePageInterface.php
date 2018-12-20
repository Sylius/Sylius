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

namespace Sylius\Behat\Page\Admin\Promotion;

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
}
