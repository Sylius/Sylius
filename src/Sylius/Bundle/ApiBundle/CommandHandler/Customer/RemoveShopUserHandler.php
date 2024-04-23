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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Customer;

use Sylius\Bundle\ApiBundle\Command\Customer\RemoveShopUser;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

final class RemoveShopUserHandler
{
    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     */
    public function __construct(
        private UserRepositoryInterface $shopUserRepository,
    ) {
    }

    public function __invoke(RemoveShopUser $removeShopUser): void
    {
        $shopUser = $this->shopUserRepository->find($removeShopUser->getShopUserId());

        if (null === $shopUser) {
            throw new UserNotFoundException();
        }

        $shopUser->setCustomer(null);
        $this->shopUserRepository->remove($shopUser);
    }
}
