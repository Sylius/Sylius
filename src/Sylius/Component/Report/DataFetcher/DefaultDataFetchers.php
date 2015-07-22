<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Report\DataFetcher;

/**
 * Default data fetchers.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DefaultDataFetchers
{
    /**
     * User registrations data fetcher.
     */
    const USER_REGISTRATION = 'user_registration';

    /**
     * Sales total data fetcher.
     */
    const SALES_TOTAL = 'sales_total';

    /**
     * Number of orders data fetcher.
     */
    const NUMBER_OF_ORDERS = 'number_of_orders';
}
