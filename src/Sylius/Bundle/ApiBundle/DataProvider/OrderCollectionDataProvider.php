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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

/** @experimental */
final class OrderCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var UserContextInterface */
    private $userContext;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(UserContextInterface $userContext, OrderRepositoryInterface $orderRepository)
    {
        $this->userContext = $userContext;
        $this->orderRepository = $orderRepository;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var ShopUserInterface $user */
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return  $this->orderRepository->findAllExceptCarts();
        }

        if ($user instanceof ShopUserInterface) {
            /** @var CustomerInterface $customer */
            $customer = $user->getCustomer();

            return $this->orderRepository->findByCustomer($customer);
        }

        return [];
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, OrderInterface::class, true);
    }
}
