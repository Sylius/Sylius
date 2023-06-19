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
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserLoginTrait
{
    protected function logInUser(string $userType, string $email): array
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->get(sprintf('sylius.repository.%s_user', $userType));
        /** @var JWTTokenManagerInterface $manager */
        $manager = $this->get('lexik_jwt_authentication.jwt_manager');

        /** @var UserInterface|null $user */
        $user = $shopUserRepository->findOneByEmail($email);

        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        return ['HTTP_' . $authorizationHeader => 'Bearer ' . $manager->create($user)];
    }
}
