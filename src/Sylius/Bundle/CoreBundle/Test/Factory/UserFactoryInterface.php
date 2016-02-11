<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Factory;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface UserFactoryInterface
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $password
     *
     * @return UserInterface
     */
    public function create($firstName, $lastName, $email, $password);
}
