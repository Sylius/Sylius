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
    /**
     * @param int|null $priority
     */
    public function setPriority(?int $priority): void;

    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function checkChannelsState(string $channelName): bool;

    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param string $limit
     */
    public function fillUsageLimit(string $limit): void;

    public function makeExclusive(): void;

    public function checkCouponBased(): void;

    /**
     * @param string $name
     */
    public function checkChannel(string $name): void;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setStartsAt(\DateTimeInterface $dateTime): void;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setEndsAt(\DateTimeInterface $dateTime): void;

    /**
     * {@inheritdoc}
     */
    public function hasStartsAt(\DateTimeInterface $dateTime);

    /**
     * {@inheritdoc}
     */
    public function hasEndsAt(\DateTimeInterface $dateTime);
}
