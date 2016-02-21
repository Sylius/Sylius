<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class RbacContext extends DefaultContext
{
    /**
     * @Given /^there is following permission hierarchy:$/
     */
    public function thereIsFollowingPermissionHierarchy(TableNode $table)
    {
        $repository = $this->getRepository('permission');
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('permission');

        foreach ($repository->findAll() as $existingPermission) {
            $manager->remove($existingPermission);
        }

        $manager->flush();

        $root = $factory->createNew();
        $root->setCode('root');
        $root->setDescription('Root');

        $manager->persist($root);
        $manager->flush();

        $permissions = [];

        foreach ($table->getHash() as $data) {
            $permission = $factory->createNew();

            $permission->setCode($data['code']);
            $permission->setDescription($data['description']);

            if (!empty($data['parent'])) {
                $permission->setParent($permissions[trim($data['parent'])]);
            } else {
                $permission->setParent($root);
            }

            $permissions[$data['code']] = $permission;

            $manager->persist($permission);
        }

        $manager->flush();
    }

    /**
     * @Given there is following role hierarchy:
     */
    public function thereIsFollowingRoleHierarchy(TableNode $table)
    {
        $repository = $this->getRepository('role');
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('role');

        foreach ($repository->findAll() as $existingRole) {
            $manager->remove($existingRole);
        }

        $manager->flush();

        $root = $factory->createNew();
        $root->setCode('root');
        $root->setName('Root');

        $manager->persist($root);
        $manager->flush();

        $roles = [];

        foreach ($table->getHash() as $data) {
            $role = $factory->createNew();

            $role->setCode($data['code']);
            $role->setName($data['name']);

            if (!empty($data['parent'])) {
                $role->setParent($roles[trim($data['parent'])]);
            } else {
                $role->setParent($root);
            }

            if (!empty($data['security roles'])) {
                $securityRoles = [];

                foreach (explode(',', $data['security roles']) as $securityRole) {
                    $securityRoles[] = trim($securityRole);
                }

                $role->setSecurityRoles($securityRoles);
            }

            $roles[$data['code']] = $role;

            $manager->persist($role);
        }

        $manager->flush();
    }

    /**
     * @Given role :role has the following permissions:
     */
    public function roleHasTheFollowingPermissions($roleName, TableNode $table)
    {
        $role = $this->findOneByName('role', $roleName);

        foreach ($table->getRows() as $permission) {
            $role->addPermission($this->findOneBy('permission', ['code' => $permission]));
        }

        $manager = $this->getEntityManager();

        $manager->persist($role);
        $manager->flush();
    }

    /**
     * @Given authorization checks are enabled
     */
    public function authorizationChecksAreEnabled()
    {
        $settingsManager = $this->getService('sylius.settings.manager');
        $settings = $settingsManager->load('sylius_security');

        $settings->set('enabled', true);

        $settingsManager->save($settings);
    }
}
