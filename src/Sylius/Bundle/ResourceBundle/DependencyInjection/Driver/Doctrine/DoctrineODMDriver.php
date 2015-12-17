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

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class DoctrineODMDriver extends AbstractDoctrineDriver
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM;
    }

    /**
     * {@inheritdoc}
     */
    protected function addRepository(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $modelClass = $metadata->getClass('model');

        $reflection = new \ReflectionClass($modelClass);
        $translatableInterface = 'Sylius\Component\Translation\Model\TranslatableInterface';
        $translatable = interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface);

        $repositoryClass = $translatable
            ? 'Sylius\Bundle\TranslationBundle\Doctrine\ODM\MongoDB\TranslatableResourceRepository'
            : new Parameter('sylius.mongodb_odm.repository.class');

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $unitOfWorkDefinition = new Definition('Doctrine\\ODM\\MongoDB\\UnitOfWork');
        $unitOfWorkDefinition
            ->setFactory(array(new Reference($this->getManagerServiceId($metadata)), 'getUnitOfWork'))
            ->setPublic(false)
        ;

        $definition = new Definition($repositoryClass);
        $definition->setArguments(array(
            new Reference($metadata->getServiceId('manager')),
            $unitOfWorkDefinition,
            $this->getClassMetadataDefinition($modelClass),
        ));

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceId(MetadataInterface $metadata)
    {
        return sprintf('doctrine_mongodb.odm.%s_document_manager', 'default');
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadata';
    }
}
