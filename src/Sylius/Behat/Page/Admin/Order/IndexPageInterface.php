<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param \DateTime $dateTime
     */
    public function specifyFilterDateFrom(\DateTime $dateTime);

    /**
     * @param \DateTime $dateTime
     */
    public function specifyFilterDateTo(\DateTime $dateTime);

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
