<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface TokenProviderInterface
{
    /**
     * Generates unique token for user request password reset
     */
    public function generateUniqueToken();
}
