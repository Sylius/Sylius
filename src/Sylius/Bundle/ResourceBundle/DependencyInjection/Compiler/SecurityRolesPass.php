<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SecurityRolesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('security.role_hierarchy.roles')) {
            return;
        }

        $roles = $container->getParameter('security.role_hierarchy.roles');
        foreach ($container->getExtensions() as $name => $extension) {
            if (false !== strpos($name, 'sylius_')) {
                $name = $this->getSecurityRoles($name);
                if (!$container->hasParameter($name)) {
                    continue;
                }

                foreach ($container->getParameter($name) as $role => $currentRoles) {
                    if (isset($roles[$role])) {
                        foreach ($currentRoles as $currentRole) {
                            if (!in_array($currentRole, $roles[$role])) {
                                $roles[$role][] = $currentRole;
                            }
                        }
                    } else {
                        $roles[$role] = $currentRoles;
                    }
                }
            }
        }

        $container->setParameter('security.role_hierarchy.roles', $roles);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getSecurityRoles($name)
    {
        return sprintf('sylius.security.roles.%s', str_replace('sylius_', '', $name));
    }
}
