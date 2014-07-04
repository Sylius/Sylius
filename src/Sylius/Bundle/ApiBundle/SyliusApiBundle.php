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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Api bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusApiBundle extends Bundle
{
    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\ApiBundle\Model\UserInterface'         => 'sylius.model.api_user.class',
            'Sylius\Bundle\ApiBundle\Model\ClientInterface'       => 'sylius.model.api_client.class',
            'Sylius\Bundle\ApiBundle\Model\AccessTokenInterface'  => 'sylius.model.api_access_token.class',
            'Sylius\Bundle\ApiBundle\Model\RefreshTokenInterface' => 'sylius.model.api_refresh_token.class',
            'Sylius\Bundle\ApiBundle\Model\AuthCodeInterface'     => 'sylius.model.api_auth_code.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_api', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Bundle\ApiBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_api.driver.doctrine/orm'));
    }
}
