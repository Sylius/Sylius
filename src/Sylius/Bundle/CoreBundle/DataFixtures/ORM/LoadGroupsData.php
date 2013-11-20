<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Group fixtures.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadGroupsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createGroup('Administrators', array('ROLE_SYLIUS_ADMIN')));
        $manager->persist($this->createGroup('Wholesale Customers'));
        $manager->persist($this->createGroup('Retail Customers'));
        $manager->persist($this->createGroup('Sales'));
        $manager->persist($this->createGroup('Suppliers'));

        $manager->flush();
    }

    private function createGroup($name, array $roles = array())
    {
        $group = $this->getGroupRepository()->createNew();

        $group->setName($name);
        $group->setRoles($roles);

        $this->setReference('Sylius.Group.'.$name, $group);

        return $group;

    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
