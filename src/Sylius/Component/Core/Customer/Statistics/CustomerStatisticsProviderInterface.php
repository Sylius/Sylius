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

namespace Sylius\Component\Core\Customer\Statistics;

use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface CustomerStatisticsProviderInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return CustomerStatistics
     */
    public function getCustomerStatistics(CustomerInterface $customer);
}
