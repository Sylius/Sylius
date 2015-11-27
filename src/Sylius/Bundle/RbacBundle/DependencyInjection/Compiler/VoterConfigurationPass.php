<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Sylius\Bundle\RbacBundle\Security\Role\Inflector;

/**
 * @author Christian Daguer.re <christian@daguer.re>
 */
class VoterConfigurationPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    protected $rolePrefix = Inflector::DEFAULT_PREFIX;

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Disallow use of "security.roles_hierarchy".
        if ($container->hasParameter('security.role_hierarchy.roles')) {
            if ($container->getParameter('security.role_hierarchy.roles')) {
                throw new InvalidArgumentException('You cant use "security.role_hierarchy" and Sylius RBAC together.');
            }
        }

        // Remove Symfony Role* voters, but keep role prefix from configuration if it was set.
        foreach (array('simple_role_voter', 'role_hierarchy_voter') as $prefixArgPosition => $voter) {
            $serviceId = sprintf('security.access.%s', $voter);

            if ($container->hasDefinition($serviceId)) {
                $args = $container->getDefinition($serviceId)->getArguments();
                if (isset($args[$prefixArgPosition]) && is_string($args[$prefixArgPosition])) {
                    $this->rolePrefix = $args[$prefixArgPosition];
                }
                $container->removeDefinition($serviceId);
            }
        }

        // Set resolved role prefix and build SecurityBundle style role hierarchy
        $container->setParameter('sylius.rbac.role_prefix', $this->rolePrefix);
        $container->setParameter('sylius.rbac.role_hierarchy', $this->buildRoleHierarchy($container));
    }

    /**
     * Build Symfony style role and permission hierarchy.
     *
     * @param  ContainerBuilder $container
     *
     * @return array
     */
    private function buildRoleHierarchy(ContainerBuilder $container)
    {
        $this->inflector = new Inflector($this->rolePrefix);
        $map = array();

        $roles = $container->getParameter('sylius.rbac.default_roles');
        $permissions = $container->getParameter('sylius.rbac.default_permissions');
        $permissionsHierarchy = $container->getParameter('sylius.rbac.default_permissions_hierarchy');

        foreach (array_keys($permissions) as $permission) {
            $this->addPermission($map, $permissionsHierarchy, $permission);
        }

        foreach (array_keys($roles) as $code) {
            $this->addRole($map, $roles, $code);
        }

        return $map;
    }

    /**
     * @param array  &$map
     * @param array  $hierarchy
     * @param string $code
     *
     * @return array
     */
    private function addPermission(array &$map, array $hierarchy, $code)
    {
        if (isset($map[$code])) {
            return $map[$code];
        }

        $map[$code] = array();

        if (isset($hierarchy[$code])) {
            foreach ($hierarchy[$code] as $childCode) {
                $this->addPermission($map, $hierarchy, $childCode);
                $map[$code] = array_merge($map[$code], array($childCode), $map[$childCode]);
            }
        }

        return $map[$code];
    }

    /**
     * @param array  &$map
     * @param array  $rolesConfig
     * @param string $code
     *
     * @return array
     */
    private function addRole(array &$map, array $rolesConfig, $code)
    {
        $formattedCode = $this->inflector->toSecurityRole($code);

        if (isset($map[$formattedCode])) {
            return $map[$formattedCode];
        }

        $map[$formattedCode] = array();

        if (isset($rolesConfig[$code]['permissions'])) {
            foreach ($rolesConfig[$code]['permissions'] as $permission) {
                $map[$formattedCode] = array_merge($map[$formattedCode], array($permission), $map[$permission]);
            }
        }

        if (isset($rolesConfig[$code]['child_roles'])) {
            foreach ($rolesConfig[$code]['child_roles'] as $childCode) {
                $this->addRole($map, $rolesConfig, $childCode);
                $formattedChildCode = $this->inflector->toSecurityRole($childCode);
                $map[$formattedCode] = array_merge($map[$formattedCode], array($childCode), $map[$formattedChildCode]);
            }
        }

        return $map[$formattedCode];
    }
}
