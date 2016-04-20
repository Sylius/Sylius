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

use Symfony\Bundle\SecurityBundle\DependencyInjection\Compiler\AddSecurityVotersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Sylius\Bundle\RbacBundle\Security\Role\Inflector;

/**
 * @author Christian Daguer.re <christian@daguer.re>
 */
class VoterConfigurationPass extends AddSecurityVotersPass
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
        $container->setParameter('sylius.rbac.role_hierarchy.roles', $this->buildRoleHierarchy($container));

        // Provide symfony role hierarchy service (used in web profiler)
        $container->setAlias('security.role_hierarchy', 'sylius.rbac.role_hierarchy');

        // Make sure voters are registered by order of priority
        parent::process($container);
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

        foreach (array_keys($permissions) as $code) {
            $this->addPermission($map, $permissions, $code);
        }

        foreach (array_keys($roles) as $code) {
            $this->addRole($map, $roles, $code);
        }

        return $map;
    }

    /**
     * @param array  &$map
     * @param array  $permissions
     * @param string $code
     *
     * @return array
     */
    private function addPermission(array &$map, array $permissions, $code)
    {
        if (isset($map[$code])) {
            return $map[$code];
        }

        $map[$code] = array();

        if (isset($permissions[$code]['child_permissions'])) {
            foreach ($permissions[$code]['child_permissions'] as $childCode) {
                if (!isset($permissions[$childCode])) {
                    throw new InvalidArgumentException(sprintf(
                        'The "%s" permission set as child permission of "%s" is not defined.',
                        $childCode,
                        $code
                    ));
                }
                $this->addPermission($map, $permissions, $childCode);
                $map[$code] = array_merge($map[$code], array($childCode), $map[$childCode]);
            }
        }

        return $map[$code];
    }

    /**
     * @param array  &$map
     * @param array  $roles
     * @param string $code
     *
     * @return array
     */
    private function addRole(array &$map, array $roles, $code)
    {
        $formattedCode = $this->inflector->toSecurityRole($code);

        if (isset($map[$formattedCode])) {
            return $map[$formattedCode];
        }

        $map[$formattedCode] = array();

        if (isset($roles[$code]['permissions'])) {
            foreach ($roles[$code]['permissions'] as $permissionCode) {
                if (!isset($map[$permissionCode])) {
                    throw new InvalidArgumentException(sprintf(
                        'The "%s" permission set on the "%s" role is not defined.',
                        $permissionCode,
                        $code
                    ));
                }
                $map[$formattedCode] = array_merge($map[$formattedCode], array($permissionCode), $map[$permissionCode]);
            }
        }

        if (isset($roles[$code]['child_roles'])) {
            foreach ($roles[$code]['child_roles'] as $childCode) {
                if (!isset($roles[$childCode])) {
                    throw new InvalidArgumentException(sprintf(
                        'The "%s" role set as child role of "%s" is not defined.',
                        $childCode,
                        $code
                    ));
                }
                $this->addRole($map, $roles, $childCode);
                $formattedChildCode = $this->inflector->toSecurityRole($childCode);
                $map[$formattedCode] = array_merge($map[$formattedCode], array($childCode), $map[$formattedChildCode]);
            }
        }

        return $map[$formattedCode];
    }
}
