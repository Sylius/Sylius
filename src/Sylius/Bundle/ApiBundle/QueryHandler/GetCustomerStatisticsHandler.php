<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\QueryHandler;

use Sylius\Bundle\ApiBundle\Query\GetCustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

final class GetCustomerStatisticsHandler
{
    /**
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private CustomerStatisticsProviderInterface $customerStatisticsProvider,
    ) {
    }

    public function __invoke(GetCustomerStatistics $query): CustomerStatistics
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->find($query->getCustomerId());

        if ($customer === null) {
            throw new CustomerNotFoundException();
        }

        return $this->customerStatisticsProvider->getCustomerStatistics($customer);
    }
}
