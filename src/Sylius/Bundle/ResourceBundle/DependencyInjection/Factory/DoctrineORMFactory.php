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

class DoctrineORMFactory extends AbstractFactory
{
    public function create($prefix, $resourceName, array $classes, $templates = null)
    {
        $pattern = $prefix.'.%s.'.$resourceName;
        $entityManagerId = 'doctrine.orm.entity_manager';

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
        $this->container->setAlias($managerId, new Alias($entityManagerId));

        $classMetadata = new Definition('Doctrine\\ORM\\Mapping\\ClassMetadata');
        $classMetadata
            ->setFactoryService('doctrine.orm.entity_manager')
            ->setFactoryMethod('getClassMetadata')
            ->setArguments(array($classes['model']))
            ->setPublic(false)
        ;

        $repositoryClassParameter = sprintf($pattern, 'repository').'.class';
        $repositoryClass = $this->container->hasParameter($repositoryClassParameter) ? $this->container->getParameter($repositoryClassParameter) : 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository';

        if (isset($classes['repository'])) {
            $repositoryClass = $classes['repository'];
        }

        $repository = new Definition($repositoryClass);
        $repository
            ->setArguments(array(new Reference($managerId), $classMetadata))
        ;

        $this->container->setDefinition(sprintf($pattern, 'repository'), $repository);
    }

    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_ORM;
    }
}
