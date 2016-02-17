<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Doctrine;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class RbacInitializer
{
    private $permissions;
    private $permissionsHierarchy;
    private $permissionManager;
    private $permissionFactory;
    private $permissionRepository;

    private $permissionsByCode = [
        'root' => null,
    ];

    private $roles;
    private $rolesHierarchy;
    private $roleManager;
    private $roleFactory;
    private $roleRepository;

    public function __construct(
        array $permissions,
        array $permissionsHierarchy,
        $permissionManager,
        FactoryInterface $permissionFactory,
        RepositoryInterface $permissionRepository,
        array $roles,
        array $rolesHierarchy,
        $roleManager,
        FactoryInterface $roleFactory,
        RepositoryInterface $roleRepository
    ) {
        $this->permissions = $permissions;
        $this->permissionsHierarchy = $permissionsHierarchy;
        $this->permissionFactory = $permissionFactory;
        $this->permissionManager = $permissionManager;
        $this->permissionRepository = $permissionRepository;

        $this->roles = $roles;
        $this->rolesHierarchy = $rolesHierarchy;
        $this->roleFactory = $roleFactory;
        $this->roleManager = $roleManager;
        $this->roleRepository = $roleRepository;
    }

    public function initialize(OutputInterface $output = null)
    {
        try {
            $this->initializePermissions($output);
            $this->initializeRoles($output);
        } catch (NonUniqueResultException $exception) {
            if ($output) {
                $output->writeln('RBAC already initialized');
            }
        }
    }

    protected function initializePermissions(OutputInterface $output = null)
    {
        if (null === $root = $this->permissionRepository->findOneBy(['code' => 'root'])) {
            $root = $this->permissionFactory->createNew();
            $root->setCode('root');
            $root->setDescription('Root');

            $this->permissionManager->persist($root);
            $this->permissionManager->flush();
        }

        $this->permissionsByCode['root'] = $root;

        foreach ($this->permissions as $code => $description) {
            if (null === $permission = $this->permissionRepository->findOneBy(['code' => $code])) {
                $permission = $this->permissionFactory->createNew();
                $permission->setCode($code);
                $permission->setDescription($description);
                $permission->setParent($root);

                $this->permissionManager->persist($permission);

                if ($output) {
                    $output->writeln(sprintf('Adding permission "<comment>%s</comment>". (<info>%s</info>)', $description, $code));
                }
            }

            $this->permissionsByCode[$code] = $permission;
        }

        foreach ($this->permissionsHierarchy as $code => $children) {
            foreach ($children as $childCode) {
                $this->permissionsByCode[$code]->addChild($this->permissionsByCode[$childCode]);
            }
        }

        $this->permissionManager->flush();
    }

    protected function initializeRoles(OutputInterface $output = null)
    {
        if (!isset($this->permissionsByCode['root'])) {
            return;
        }

        if (null === $root = $this->roleRepository->findOneBy(['code' => 'root'])) {
            $root = $this->roleFactory->createNew();
            $root->setCode('root');
            $root->setName('Root');

            $root->addPermission($this->permissionsByCode['root']);

            $this->roleManager->persist($root);
            $this->roleManager->flush();
        }

        $rolesByCode = ['root' => $root];

        foreach ($this->roles as $code => $data) {
            if (null === $role = $this->roleRepository->findOneBy(['code' => $code])) {
                $role = $this->roleFactory->createNew();
                $role->setCode($code);
                $role->setName($data['name']);
                $role->setDescription($data['description']);
                $role->setParent($root);
                if ($output) {
                    $output->writeln(sprintf('Adding role "<comment>%s</comment>". (<info>%s</info>)', $data['name'], $code));
                }
            }

            foreach ($data['permissions'] as $permission) {
                if (!$role->hasPermission($this->permissionsByCode[$permission])) {
                    $role->addPermission($this->permissionsByCode[$permission]);
                    if ($output) {
                        $output->writeln(sprintf('Adding role:permission <info>%s</info>:<comment>%s</comment>',
                            $role->getCode(),
                            $permission
                        ));
                    }
                }
            }

            $role->setSecurityRoles($data['security_roles']);

            $this->roleManager->persist($role);

            $rolesByCode[$code] = $role;
        }

        foreach ($this->rolesHierarchy as $code => $children) {
            foreach ($children as $childCode) {
                $rolesByCode[$code]->addChild($rolesByCode[$childCode]);
            }
        }

        $this->roleManager->flush();
    }
}
