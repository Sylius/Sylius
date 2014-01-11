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

        $controller = new Definition($classes['controller']);
        $controller
            ->setArguments(array($prefix, $resourceName, $templates))
            ->addMethodCall('setContainer', array(new Reference('service_container')))
        ;

        $managerId = sprintf($pattern, 'manager');
        $this->container->setDefinition(sprintf($pattern, 'controller'), $controller);
        $this->container->setAlias($managerId, new Alias($entityManagerId));

        $classMetadata = new Definition('Doctrine\\ORM\\Mapping\\ClassMetadata');
        $classMetadata
            ->setFactoryService('doctrine.orm.entity_manager')
            ->setFactoryMethod('getClassMetadata')
            ->setArguments(array($classes['model']))
            ->setPublic(false)
        ;

        $repositoryClass = isset($classes['repository']) ? $classes['repository'] : 'Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository';
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