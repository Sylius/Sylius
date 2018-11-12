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
    public function setPriority(?int $priority);

    public function getPriority(): int;

    public function nameIt(string $name);

    public function checkChannelsState(string $channelName): bool;

    public function isCodeDisabled(): bool;

    public function fillUsageLimit(string $limit);

    public function makeExclusive();

    public function checkCouponBased();

    public function checkChannel(string $name);

    public function setStartsAt(\DateTimeInterface $dateTime);

    public function setEndsAt(\DateTimeInterface $dateTime);

    /**
     * {@inheritdoc}
     */
    public function hasStartsAt(\DateTimeInterface $dateTime);

    /**
     * {@inheritdoc}
     */
    public function hasEndsAt(\DateTimeInterface $dateTime);
}
