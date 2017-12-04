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
    public function setPriority($priority);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function checkChannelsState($channelName);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param string $limit
     */
    public function fillUsageLimit($limit);

    public function makeExclusive();

    public function checkCouponBased();

    /**
     * @param string $name
     */
    public function checkChannel($name);

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function setStartsAt(\DateTimeInterface $dateTime);

    /**
     * @param \DateTimeInterface $dateTime
     */
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
