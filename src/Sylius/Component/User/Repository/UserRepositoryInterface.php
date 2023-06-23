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

namespace Sylius\Component\User\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @template T of UserInterface
 *
 * @extends RepositoryInterface<T>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findOneByEmail(string $email): ?UserInterface;
}
