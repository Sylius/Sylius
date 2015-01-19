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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DoctrineORMDriver extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    protected function getRepositoryDefinition(ResourceMetadataInterface $metadata, ContainerBuilder $container)
    {
        $reflection = new \ReflectionClass($metadata->getClass('model'));
        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';
        $translatable = (interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface));

        $repositoryClassParameter = $metadata->getServiceId('repository').'.class';

        $repositoryClass = $translatable ?
            new Parameter('sylius.doctrine.orm.translatable_repository.class') :
            new Parameter('sylius.doctrine.orm.repository.class')
        ;

        if ($container->hasParameter($repositoryClassParameter)) {
            $repositoryClass = $container->getParameter($repositoryClassParameter);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $repositoryDefinition = new Definition('Doctrine\ORM\EntityRepository');
        $repositoryDefinition->setFactoryService($this->getObjectManagerId($metadata));
        $repositoryDefinition->setFactoryMethod('getRepository');
        $repositoryDefinition->setArguments(array($metadata->getClass('model')));

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            $repositoryDefinition,
            new Reference($this->getObjectManagerId($metadata)),
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectManagerId(ResourceMetadataInterface $metadata)
    {
        return sprintf('doctrine.orm.%s_entity_manager', $metadata->getParameter('manager', 'default'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_ORM;
    }

}
