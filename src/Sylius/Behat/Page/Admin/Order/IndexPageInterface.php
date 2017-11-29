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

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param \DateTimeInterface $dateTime
     */
    public function specifyFilterDateFrom(\DateTimeInterface $dateTime): void;

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function specifyFilterDateTo(\DateTimeInterface $dateTime): void;

    /**
     * @param string $channelName
     */
    public function chooseChannelFilter(string $channelName): void;

    /**
     * @param string $currencyName
     */
    public function chooseCurrencyFilter(string $currencyName): void;

    /**
     * @param string $total
     */
    public function specifyFilterTotalGreaterThan(string $total): void;

    /**
     * @param string $total
     */
    public function specifyFilterTotalLessThan(string $total): void;
}
