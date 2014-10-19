<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Api bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusApiBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_api';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Bundle\ApiBundle\Model\UserInterface'         => 'sylius.model.api_user.class',
            'Sylius\Bundle\ApiBundle\Model\ClientInterface'       => 'sylius.model.api_client.class',
            'Sylius\Bundle\ApiBundle\Model\AccessTokenInterface'  => 'sylius.model.api_access_token.class',
            'Sylius\Bundle\ApiBundle\Model\RefreshTokenInterface' => 'sylius.model.api_refresh_token.class',
            'Sylius\Bundle\ApiBundle\Model\AuthCodeInterface'     => 'sylius.model.api_auth_code.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Bundle\ApiBundle\Model';
    }
}
