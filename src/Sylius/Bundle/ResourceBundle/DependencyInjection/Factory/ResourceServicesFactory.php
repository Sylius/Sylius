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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Resources services factory.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ResourceServicesFactory
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function create($prefix, $resourceName, $driver, array $classes, $templates = null)
    {
        if (SyliusResourceBundle::DRIVER_DOCTRINE_ORM === $driver) {
            $entityManagerId = 'doctrine.orm.entity_manager';

            $controller = new Definition($classes['controller']);
            $controller
                ->setArguments(array(new Reference(sprintf($prefix.'.%s.'.$resourceName, 'config'))))
                ->addMethodCall('setContainer', array(new Reference('service_container')));

            $classMetadata = new Definition('Doctrine\\ORM\\Mapping\\ClassMetadata');
            $classMetadata
                ->setFactoryService($entityManagerId)
                ->setFactoryMethod('getClassMetadata')
                ->setArguments(array($classes['model']))
                ->setPublic(false);

            $repositoryClass = isset($classes['repository']) ? $classes['repository'] : 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository';
            $repository = new Definition($repositoryClass);
            $repository->setArguments(array(new Reference($entityManagerId), $classMetadata));

            $config = new Definition('Sylius\\Bundle\\ResourceBundle\\Controller\\Configuration');
            $config->setArguments(array(new Reference('service_container'), $prefix, $resourceName, $templates));

            $this->container->setAlias(sprintf($prefix.'.%s.'.$resourceName, 'manager'), new Alias($entityManagerId));

            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'repository'), $repository);
            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'controller'), $controller);
            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'config'), $config);
        } elseif (SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM === $driver) {
            $documentManagerId = 'doctrine.odm.mongodb.document_manager';

            $controller = new Definition($classes['controller']);
            $controller
                ->setArguments(array(new Reference(sprintf($prefix.'.%s.'.$resourceName, 'config'))))
                ->addMethodCall('setContainer', array(new Reference('service_container')));

            $classMetadata = new Definition('Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadata');
            $classMetadata
                ->setFactoryService($documentManagerId)
                ->setFactoryMethod('getClassMetadata')
                ->setArguments(array($classes['model']))
                ->setPublic(false);

            $unitOfWork = new Definition('Doctrine\\ODM\\MongoDB\\UnitOfWork');
            $unitOfWork
                ->setFactoryService($documentManagerId)
                ->setFactoryMethod('getUnitOfWork')
                ->setPublic(false);

            $repositoryClass = isset($classes['repository']) ? $classes['repository'] : 'Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository';
            $repository = new Definition($repositoryClass);
            $repository->setArguments(array(new Reference($entityManagerId), $unitOfWork, $classMetadata));

            $config = new Definition('Sylius\\Bundle\\ResourceBundle\\Controller\\Configuration');
            $config->setArguments(array(new Reference('service_container'), $prefix, $resourceName, $templates));

            $this->container->setAlias(sprintf($prefix.'.%s.'.$resourceName, 'manager'), new Alias($documentManagerId));

            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'repository'), $repository);
            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'controller'), $controller);
            $this->container->setDefinition(sprintf($prefix.'.%s.'.$resourceName, 'config'), $config);
        }
    }
}