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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface SecurityServiceInterface
{
    const DEFAULT_PROVIDER_KEY = 'main';

    /**
     * @param string $email
     * @param Session $minkSession
     * @param string $providerKey
     *
     * @throws \InvalidArgumentException
     */
    public function logIn($email, Session $minkSession, $providerKey = self::DEFAULT_PROVIDER_KEY);
}
