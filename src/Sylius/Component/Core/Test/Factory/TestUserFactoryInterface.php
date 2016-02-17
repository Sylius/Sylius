<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface TestUserFactoryInterface
{
    /**
     * @param string $email
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     *
     * @return UserInterface
     */
    public function create($email, $password, $firstName, $lastName);

    /**
     * @return UserInterface
     */
    public function createDefault();
}
