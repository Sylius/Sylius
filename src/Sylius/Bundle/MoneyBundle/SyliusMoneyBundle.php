<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Money bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusMoneyBundle extends Bundle
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
            'Sylius\Component\Money\Model\ExchangeRateInterface' => 'sylius.model.exchange_rate.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_money', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Component\Money\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_money.driver.doctrine/orm'));
    }
}
