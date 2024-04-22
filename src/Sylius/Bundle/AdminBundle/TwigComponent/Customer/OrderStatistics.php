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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Customer;

use Sylius\Bundle\AdminBundle\TwigComponent\HookableComponentTrait;
use Sylius\Component\Core\Customer\Statistics\CustomerStatisticsProviderInterface;
use Sylius\Component\Core\Customer\Statistics\PerChannelCustomerStatistics;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(
    name: 'SyliusAdmin.Customer.Show.OrderStatistics',
    template: '@SyliusAdmin/Customer/Show/Component/orderStatistics.html.twig',
)]
final class OrderStatistics
{
    use HookableComponentTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $customerId = null;

    /**
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CustomerStatisticsProviderInterface $statisticsProvider,
    ) {
    }

    /**
     * @return PerChannelCustomerStatistics[]
     */
    #[ExposeInTemplate]
    public function getStatistics(): array
    {
        $customer = $this->customerRepository->find($this->customerId);
        if (null === $customer) {
            return [];
        }

        return $this->statisticsProvider->getCustomerStatistics($customer)->getPerChannelsStatistics();
    }
}
