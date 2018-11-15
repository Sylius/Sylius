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
     * @throws \InvalidArgumentException
     */
    public function logIn(UserInterface $user);

    public function logOut();

    /**
     * @return TokenInterface
     *
     * @throws TokenNotFoundException
     */
    public function getCurrentToken();

    public function restoreToken(TokenInterface $token);
}
