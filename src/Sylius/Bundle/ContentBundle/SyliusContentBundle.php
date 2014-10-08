<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Sylius content bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusContentBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'         => array(
                'ROLE_SYLIUS_CONTENT_ADMIN',
            ),
            'ROLE_SYLIUS_CONTENT_ADMIN' => array(
                'ROLE_SYLIUS_CONTENT_LIST',
                'ROLE_SYLIUS_CONTENT_SHOW',
                'ROLE_SYLIUS_CONTENT_CREATE',
                'ROLE_SYLIUS_CONTENT_UPDATE',
                'ROLE_SYLIUS_CONTENT_DELETE',
            ),
        );
    }
}
