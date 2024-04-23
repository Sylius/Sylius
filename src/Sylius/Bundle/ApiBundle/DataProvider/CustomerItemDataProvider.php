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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final class CustomerItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(
        private UserContextInterface $userContext,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        /** @var ShopUserInterface|null $user */
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && $user instanceof SymfonyUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->customerRepository->find($id);
        }

        if (
            $user instanceof ShopUserInterface &&
            $id === $user->getCustomer()->getId()
        ) {
            return $this->customerRepository->find($id);
        }

        if ($user === null && $operationName === 'shop_verify_customer_account') {
            return $this->customerRepository->find($id);
        }

        return null;
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, CustomerInterface::class, true);
    }
}
