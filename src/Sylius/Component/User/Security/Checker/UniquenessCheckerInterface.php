<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security\Checker;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface UniquenessCheckerInterface
{
    /**
     * @param string $token
     *
     * @return bool
     */
    public function isUnique($token);
}
