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

namespace Sylius\Component\User\Security\Checker;

interface UniquenessCheckerInterface
{
    /**
     * @param string $token
     *
     * @return bool
     */
    public function isUnique(string $token): bool;
}
