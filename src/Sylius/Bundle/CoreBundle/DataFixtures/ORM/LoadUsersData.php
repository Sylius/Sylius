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

/**
 * User fixtures.s
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadUsersData extends AbstractDataFixture
{
    /**
     * {@inheritdoc}
     */
    protected function getFixtures()
    {
        return  array(
            __DIR__ . '/../DATA/users.yml',

        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getProcessors()
    {
        $userManager = $this->get('fos_user.user_manager');
        return array(new UserProcessor($userManager));
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

}