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

namespace spec\Sylius\Bundle\ApiBundle\QueryHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Query\GetCustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatistics;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;

final class GetCustomerStatisticsHandlerSpec extends ObjectBehavior
{
    function let(CustomerRepositoryInterface $customerRepository, CustomerStatisticsProviderInterface $customerStatisticsProvider): void
    {
        $this->beConstructedWith($customerRepository, $customerStatisticsProvider);
    }

    function it_returns_statistics_for_a_given_customer(
        CustomerRepositoryInterface $customerRepository,
        CustomerStatisticsProviderInterface $customerStatisticsProvider,
        CustomerInterface $customer,
    ): void {
        $customerStatistics = new CustomerStatistics([]);

        $customerRepository->find(1)->willReturn($customer);
        $customerStatisticsProvider->getCustomerStatistics($customer)->willReturn($customerStatistics);

        $query = new GetCustomerStatistics(1);
        $this($query)->shouldReturn($customerStatistics);
    }

    function it_throws_an_exception_when_customer_with_a_given_id_doesnt_exist(CustomerRepositoryInterface $customerRepository): void
    {
        $customerRepository->find(1)->willReturn(null);

        $query = new GetCustomerStatistics(1);

        $this
            ->shouldThrow(CustomerNotFoundException::class)
            ->during('__invoke', [$query])
        ;
    }
}
