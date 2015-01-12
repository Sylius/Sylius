<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Locale bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusLocaleBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN'        => array(
                'ROLE_SYLIUS_LOCALE_ADMIN',
            ),
            'ROLE_SYLIUS_LOCALE_ADMIN' => array(
                'ROLE_SYLIUS_LOCALE_LIST',
                'ROLE_SYLIUS_LOCALE_SHOW',
                'ROLE_SYLIUS_LOCALE_CREATE',
                'ROLE_SYLIUS_LOCALE_UPDATE',
                'ROLE_SYLIUS_LOCALE_DELETE',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Locale\Model\LocaleInterface' => 'sylius.model.locale.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Locale\Model';
    }
}
