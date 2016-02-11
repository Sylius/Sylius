<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Services;

use Behat\Mink\Session;
use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SecurityServiceInterface
{
    /**
     * @param string $email
     * @param string $providerKey
     * @param Session $minkSession
     *
     * @throws \InvalidArgumentException
     */
    public function logIn($email, $providerKey, Session $minkSession);

    /**
     * @param Session $minkSession
     *
     * @return UserInterface
     */
    public function logInDefaultUser(Session $minkSession);
}
