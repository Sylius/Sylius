<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface UserImpersonatorInterface
{
    /**
     * @param UserInterface $user
     */
    public function impersonate(UserInterface $user);
}
