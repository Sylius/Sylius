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

namespace Sylius\Bundle\UserBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UsernameProvider extends AbstractUserProvider
{
    /**
     * {@inheritdoc}
     */
    protected function findUser(string $username): ?UserInterface
    {
        return $this->userRepository->findOneBy(['usernameCanonical' => $username]);
    }
}
