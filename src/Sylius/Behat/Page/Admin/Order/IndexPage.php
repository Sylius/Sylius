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

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function specifyFilterDateFrom(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('criteria_date_from_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('criteria_date_from_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function specifyFilterDateTo(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('criteria_date_to_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('criteria_date_to_time', date('H:i', $timestamp));
    }
}
