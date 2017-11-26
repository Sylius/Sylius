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

namespace Sylius\Behat\Service;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

interface SecurityServiceInterface
{
    /**
     * @param UserInterface $user
     *
     * @throws \InvalidArgumentException
     */
    public function logIn(UserInterface $user): void;

    public function logOut(): void;

    /**
     * @return TokenInterface
     *
     * @throws TokenNotFoundException
     */
    public function getCurrentToken(): TokenInterface;

    /**
     * @param TokenInterface $token
     */
    public function restoreToken(TokenInterface $token): void;
}
