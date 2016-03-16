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

use Sylius\Bundle\ApiBundle\Model\AccessTokenInterface;
use Sylius\Bundle\ApiBundle\Model\AuthCodeInterface;
use Sylius\Bundle\ApiBundle\Model\ClientInterface;
use Sylius\Bundle\ApiBundle\Model\RefreshTokenInterface;
use Sylius\Bundle\ApiBundle\Model\UserInterface;
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
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            UserInterface::class => 'sylius.model.api_user.class',
            ClientInterface::class => 'sylius.model.api_client.class',
            AccessTokenInterface::class => 'sylius.model.api_access_token.class',
            RefreshTokenInterface::class => 'sylius.model.api_refresh_token.class',
            AuthCodeInterface::class => 'sylius.model.api_auth_code.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Bundle\ApiBundle\Model';
    }
}
