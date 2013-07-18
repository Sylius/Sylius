<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Flexible categorization system.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxonomiesBundle extends Bundle
{
    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface' => 'sylius.model.taxonomy.class',
            'Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface'    => 'sylius.model.taxon.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_taxonomies', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Bundle\TaxonomiesBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_taxonomies.driver.doctrine/orm'));
    }
}
