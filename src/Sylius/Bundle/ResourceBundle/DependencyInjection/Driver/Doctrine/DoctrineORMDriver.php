<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\Form\Builder\DefaultFormBuilder;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class DoctrineORMDriver extends AbstractDoctrineDriver
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_ORM;
    }

    /**
     * {@inheritdoc}
     */
    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $repositoryClassParameterName = sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryClass = EntityRepository::class;

        if ($container->hasParameter($repositoryClassParameterName)) {
            $repositoryClass = $container->getParameter($repositoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
            new Reference($metadata->getServiceId('manager')),
            $this->getClassMetadataDefinition($metadata),
        ]);

        $container->setDefinition($metadata->getServiceId('repository'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceId(MetadataInterface $metadata)
    {
        if ($objectManagerName = $this->getObjectManagerName($metadata)) {
            return sprintf('doctrine.orm.%s_entity_manager', $objectManagerName);
        }

        return 'doctrine.orm.entity_manager';
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ORM\\Mapping\\ClassMetadata';
    }
}
