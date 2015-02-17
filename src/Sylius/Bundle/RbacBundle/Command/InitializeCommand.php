<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InitializeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:rbac:initialize')
            ->setDescription('Initialize default roles and permissions in the app.')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command initializes default RBAC setup.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Initializing Sylius RBAC roles and permissions.');

        $permissions = $this->getContainer()->getParameter('sylius.rbac.default_permissions');

        $permissionManager = $this->getContainer()->get('sylius.manager.permission');
        $permissionRepository = $this->getContainer()->get('sylius.repository.permission');

        // Create root permission.
        $root = $permissionRepository->createNew();
        $root->setCode('root');
        $root->setDescription('Root');

        $permissionManager->persist($root);
        $permissionManager->flush();

        $permissionsByCode = array('root' => $root);

        foreach ($permissions as $code => $description) {
            if (null === $permissionRepository->findOneBy(array('code' => $code))) {
                $permission = $permissionRepository->createNew();

                $permission->setCode($code);
                $permission->setDescription($description);
                $permission->setParent($root);

                $permissionsByCode[$code] = $permission;

                $permissionManager->persist($permission);

                $output->writeln(sprintf('Adding permission "<comment>%s</comment>". (<info>%s</info>)', $description, $code));
            }
        }

        $permissionsHierarchy = $this->getContainer()->getParameter('sylius.rbac.default_permissions_hierarchy');

        foreach ($permissionsHierarchy as $code => $children) {
            foreach ($children as $childCode) {
                $permissionsByCode[$code]->addChild($permissionsByCode[$childCode]);
            }
        }

        $permissionManager->flush();

        $roles = $this->getContainer()->getParameter('sylius.rbac.default_roles');

        $roleManager = $this->getContainer()->get('sylius.manager.role');
        $roleRepository = $this->getContainer()->get('sylius.repository.role');

        // Create root role.
        $root = $roleRepository->createNew();
        $root->setCode('root');
        $root->setName('Root');

        $root->addPermission($permissionsByCode['root']);

        $roleManager->persist($root);
        $roleManager->flush();

        $rolesByCode = array('root' => $root);

        foreach ($roles as $code => $data) {
            if (null === $roleRepository->findOneBy(array('code' => $code))) {
                $role = $roleRepository->createNew();

                $role->setCode($code);
                $role->setName($data['name']);
                $role->setDescription($data['description']);
                $role->setParent($root);

                foreach ($data['permissions'] as $permission) {
                    $role->addPermission($permissionsByCode[$permission]);
                }

                $role->setSecurityRoles($data['security_roles']);

                $rolesByCode[$code] = $role;

                $roleManager->persist($role);

                $output->writeln(sprintf('Adding role "<comment>%s</comment>". (<info>%s</info>)', $data['name'], $code));
            }
        }

        $rolesHierarchy = $this->getContainer()->getParameter('sylius.rbac.default_roles_hierarchy');

        foreach ($rolesHierarchy as $code => $children) {
            foreach ($children as $childCode) {
                $rolesByCode[$code]->addChild($rolesByCode[$childCode]);
            }
        }

        $roleManager->flush();

        $output->writeln('<info>Completed!</info>');
    }
}
