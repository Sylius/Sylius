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
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrineODMDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDriver()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $reflection = new \ReflectionClass('...');

        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';
        $translatable = (interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface));

        $repositoryClass = $translatable
            ? new Parameter('sylius.mongodb_odm.translatable_repository.class')
            : new Parameter('sylius.mongodb_odm.repository.class');

        $repositoryKey = $this->getContainerKey('repository', '.class');

        if ($this->container->hasParameter($repositoryKey)) {
            $repositoryClass = $this->container->getParameter($repositoryKey);
        }

        if (isset($classes['repository'])) {
            $repositoryClass = $classes['repository'];
        }

        $doctrineDefinition = new Definition('Doctrine\ODM\MongoDB\DocumentRepository');
        $doctrineDefinition->setFactoryService(new Reference($this->getManagerServiceKey()));
        $doctrineDefinition->setFactoryMethod('getRepository');
        $doctrineDefinition->setArguments(array($classes['model']));

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array($doctrineDefinition, new Reference($this->getManagerServiceKey())));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectManagerId(ResourceMetadataInterface $metadata)
    {
        return sprintf('doctrine_mongodb.odm.%s_document_manager', $metadata->getParameter('manager', 'default'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM;
    }


}
