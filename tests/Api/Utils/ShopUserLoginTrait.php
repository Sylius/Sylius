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

namespace Sylius\Tests\Api\Utils;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

trait ShopUserLoginTrait
{
    protected function logInShopUser(string $email): string
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->get('sylius.repository.shop_user');
        /** @var JWTTokenManagerInterface $manager */
        $manager = $this->get('lexik_jwt_authentication.jwt_manager');

        /** @var ShopUserInterface|null $shopUser */
        $shopUser = $shopUserRepository->findOneByEmail($email);

        return $manager->create($shopUser);
    }
}
