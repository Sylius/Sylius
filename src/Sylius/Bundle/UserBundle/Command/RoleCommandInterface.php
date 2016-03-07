<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Command;

use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
interface RoleCommandInterface
{
    /**
     * @param OutputInterface $output
     * @param UserInterface $user
     * @param array $roles
     * @param array $securityRoles
     */
    public function executeRoleCommand(OutputInterface $output, UserInterface $user, array $roles, array $securityRoles);
}
