<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * User repository interface.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Finds user by email
     *
     * @param string $email
     *
     * @return null|UserInterface User object or null
     */
    public function findOneByEmail($email);
}
