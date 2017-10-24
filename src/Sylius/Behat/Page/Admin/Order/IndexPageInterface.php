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
    public function specifyFilterDateFrom(\DateTimeInterface $dateTime);

    /**
     * @param \DateTimeInterface $dateTime
     */
    public function specifyFilterDateTo(\DateTimeInterface $dateTime);

    /**
     * @param string $channelName
     */
    public function chooseChannelFilter($channelName);

    /**
     * @param string $currencyName
     */
    public function chooseCurrencyFilter($currencyName);

    /**
     * @param string $total
     */
    public function specifyFilterTotalGreaterThan($total);

    /**
     * @param string $total
     */
    public function specifyFilterTotalLessThan($total);
}
