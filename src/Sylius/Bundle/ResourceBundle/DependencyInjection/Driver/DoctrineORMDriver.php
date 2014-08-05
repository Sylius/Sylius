<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrineORMDriver extends AbstractDatabaseDriver
{
    protected $repositoryClass = 'Sylius\\Bundle\\ResourceBundle\\Doctrine\\ORM\\EntityRepository';

    /**
     * {@inheritdoc}
     */
    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerDefinition(array $classes)
    {
        $managerKey = $this->getContainerKey('manager', '.class');

        if (isset($classes['manager'])) {
            $this->managerClass = $classes['manager'];
        } elseif ($this->container->hasParameter($managerKey)) {
            $this->managerClass = $this->container->getParameter($managerKey);
        }

        $definition = new Definition($this->managerClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            new Reference('event_dispatcher'),
            $this->prefix,
            $this->resourceName,
            $this->getClassMetadataDefinition($classes['model'])
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(array $classes)
    {
        $repositoryKey = $this->getContainerKey('repository', '.class');

        if (isset($classes['repository'])) {
            $this->repositoryClass = $classes['repository'];
        } elseif ($this->container->hasParameter($repositoryKey)) {
            $this->repositoryClass = $this->container->getParameter($repositoryKey);
        }

        $definition = new Definition($this->repositoryClass);
        $definition->setArguments(array(
            new Reference($this->getContainerKey('manager')),
            $this->getClassMetadataDefinition($classes['model'])
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceKey()
    {
        return sprintf('doctrine.orm.%s_entity_manager', $this->managerName);
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ORM\\Mapping\\ClassMetadata';
    }
}
