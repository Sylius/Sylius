<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomizationBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Customization Bundle
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SyliusCustomizationBundle extends Bundle
{
    /**
     * Return array with currently supported drivers.
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
            'Sylius\Component\Customization\Model\CustomizationInterface'                => 'sylius.model.customization.class',
            'Sylius\Component\Customization\Model\CustomizationValueInterface'           => 'sylius.model.customization_value.class',
            'Sylius\Component\Customization\Model\CustomizationSubjectInterface'         => 'sylius.model.customization_subject.class',
            'Sylius\Component\Customization\Model\CustomizationSubjectInstanceInterface' => 'sylius.model.customization_subject_instance.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_customization', $interfaces));

        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Sylius\Component\Customization\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_customization.driver.doctrine/orm'));
    }
}
