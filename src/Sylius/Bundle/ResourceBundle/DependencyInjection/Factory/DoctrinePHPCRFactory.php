<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Factory;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DoctrinePHPCRFactory extends AbstractFactory
{
    public function create($prefix, $resourceName, array $classes, $templates = null)
    {
        $pattern = $prefix.'.%s.'.$resourceName;
        $documentManagerId = 'doctrine_phpcr.odm.document_manager';

        $configuration = new Definition('Sylius\Bundle\ResourceBundle\Controller\Configuration');
        $configuration
            ->setFactoryService('sylius.controller.configuration_factory')
            ->setFactoryMethod('createConfiguration')
            ->setArguments(array($prefix, $resourceName, $templates))
            ->setPublic(false)
        ;
        $controller = new Definition($classes['controller']);
        $controller
            ->setArguments(array($configuration))
            ->addMethodCall('setContainer', array(new Reference('service_container')))
        ;

        $this->container->setDefinition(sprintf($pattern, 'controller'), $controller);

        $managerId = sprintf($pattern, 'manager');
        $this->container->setDefinition(sprintf($pattern, 'controller'), $controller);
        $this->container->setAlias($managerId, new Alias($documentManagerId));

        $classMetadata = new Definition('Doctrine\\ODM\\PHPCR\\Mapping\\ClassMetadata');
        $classMetadata
            ->setFactoryService('doctrine_phpcr.odm.document_manager')
            ->setFactoryMethod('getClassMetadata')
            ->setArguments(array($classes['model']))
            ->setPublic(false)
        ;

        $repositoryClass = isset($classes['repository']) ? $classes['repository'] : 'Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository';
        $repository = new Definition($repositoryClass);
        $repository
            ->setArguments(array(new Reference($managerId), $classMetadata))
        ;

        $this->container->setDefinition(sprintf($pattern, 'repository'), $repository);
    }

    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_PHPCR_ODM;
    }
}
