<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Services generator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ServiceGenerator
{
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function generate($prefix, $resourceName, $driver, $class)
    {
        if (SyliusResourceBundle::DRIVER_DOCTRINE_ORM === $driver) {
            $pattern = $prefix.'.%s.'.$resourceName;

            $controller = new Definition('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController');
            $controller
                ->setArguments(array($prefix, $resourceName, ''))
                ->addMethodCall('setContainer', array(new Reference('service_container')))
            ;

            $this->container->setDefinition(sprintf($pattern, 'controller'), $controller);

            $manager = new Definition('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ResourceManager');
            $manager->setArguments(array(new Reference('doctrine.orm.entity_manager'), $class));

            $this->container->setDefinition(sprintf($pattern, 'manager'), $manager);

            $doctrineRepository = new Definition('Doctrine\\Common\\Persistence\\ObjectRepository');
            $doctrineRepository
                ->setFactoryService('doctrine.orm.entity_manager')
                ->setFactoryMethod('getRepository')
                ->setArguments(array($class))
                ->setPublic(false)
            ;

            $doctrineRepositoryServiceName = sprintf($pattern, 'doctrine.repository');
            $this->container->setDefinition($doctrineRepositoryServiceName, $doctrineRepository);

            $repository = new Definition('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\ResourceRepository');
            $repository->setArguments(array(new Reference($doctrineRepositoryServiceName), $class));

            $this->container->setDefinition(sprintf($pattern, 'repository'), $repository);
        } else if (SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM === $driver) {
            $pattern = $prefix.'.%s.'.$resourceName;

            $controller = new Definition('Sylius\\Bundle\\ResourceBundle\\Controller\\ResourceController');
            $controller
                ->setArguments(array($prefix, $resourceName, ''))
                ->addMethodCall('setContainer', array(new Reference('service_container')))
            ;

            $this->container->setDefinition(sprintf($pattern, 'controller'), $controller);

            $manager = new Definition('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ResourceManager');
            $manager->setArguments(array(new Reference('doctrine.odm.mongodb.document_manager'), $class));

            $this->container->setDefinition(sprintf($pattern, 'manager'), $manager);

            $doctrineRepository = new Definition('Doctrine\\Common\\Persistence\\ObjectRepository');
            $doctrineRepository
                ->setFactoryService('doctrine.odm.mongodb.document_manager')
                ->setFactoryMethod('getRepository')
                ->setArguments(array($class))
                ->setPublic(false)
            ;

            $doctrineRepositoryServiceName = sprintf($pattern, 'doctrine.repository');
            $this->container->setDefinition($doctrineRepositoryServiceName, $doctrineRepository);

            $repository = new Definition('Sylius\\Bundle\\ResourceBundle\\Doctrine\\ODM\\MongoDB\\ResourceRepository');
            $repository->setArguments(array(new Reference($doctrineRepositoryServiceName), $class));

            $this->container->setDefinition(sprintf($pattern, 'repository'), $repository);
        }
    }
}
