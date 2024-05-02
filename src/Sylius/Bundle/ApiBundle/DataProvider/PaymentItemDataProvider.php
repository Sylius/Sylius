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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class PaymentItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private UserContextInterface $userContext,
    ) {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, PaymentInterface::class, true);
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;

        if ($customer !== null && in_array('ROLE_USER', $user->getRoles(), true)) {
            return $this->paymentRepository->findOneByCustomer($id, $customer);
        }

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->paymentRepository->find($id);
        }

        return null;
    }
}
