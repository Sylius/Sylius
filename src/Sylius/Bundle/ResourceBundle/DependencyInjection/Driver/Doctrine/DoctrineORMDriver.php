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
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Translation\Model\TranslatableInterface;
use Sylius\Component\Translation\Repository\TranslatableResourceRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class DoctrineORMDriver extends AbstractDoctrineDriver
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
        $reflection = new \ReflectionClass($metadata->getClass('model'));

        $translatableInterface = TranslatableInterface::class;
        $translatable = interface_exists($translatableInterface) && $reflection->implementsInterface($translatableInterface);

        $repositoryClassParameterName = sprintf('%s.repository.%s.class', $metadata->getApplicationName(), $metadata->getName());
        $repositoryClass = $translatable
            ? TranslatableResourceRepository::class
            : EntityRepository::class
        ;

        if ($container->hasParameter($repositoryClassParameterName)) {
            $repositoryClass = $container->getParameter($repositoryClassParameterName);
        }

        if ($metadata->hasClass('repository')) {
            $repositoryClass = $metadata->getClass('repository');
        }

        $repositoryReflection = new \ReflectionClass($repositoryClass);

        $definition = new Definition($repositoryClass);
        $definition->setArguments([
            new Reference($metadata->getServiceId('manager')),
            $this->getClassMetadataDefinition($metadata),
        ]);
        $definition->setLazy(!$repositoryReflection->isFinal());

        if ($metadata->hasParameter('translation')) {
            $translatableRepositoryInterface = TranslatableResourceRepositoryInterface::class;
            $translationConfig = $metadata->getParameter('translation');

            if (interface_exists($translatableRepositoryInterface) && $repositoryReflection->implementsInterface($translatableRepositoryInterface)) {
                if (isset($translationConfig['fields'])) {
                    $definition->addMethodCall('setTranslatableFields', [$translationConfig['fields']]);
                }
            }
        }

        $container->setDefinition($metadata->getServiceId('repository'), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function addDefaultForm(ContainerBuilder $container, MetadataInterface $metadata)
    {
        $defaultFormBuilderDefinition = new Definition(DefaultFormBuilder::class);
        $defaultFormBuilderDefinition->setArguments([new Reference($metadata->getServiceId('manager'))]);

        $definition = new Definition(DefaultResourceType::class);
        $definition
            ->setArguments([
                $this->getMetdataDefinition($metadata),
                $defaultFormBuilderDefinition,
            ])
            ->addTag('form.type', ['alias' => sprintf('%s_%s', $metadata->getApplicationName(), $metadata->getName())])
        ;

        $container->setDefinition(sprintf('%s.form.type.%s', $metadata->getApplicationName(), $metadata->getName()), $definition);
    }

    /**
     * {@inheritdoc}
     */
    protected function getManagerServiceId(MetadataInterface $metadata)
    {
        return sprintf('doctrine.orm.%s_entity_manager', $this->getObjectManagerName($metadata));
    }

    /**
     * {@inheritdoc}
     */
    protected function getClassMetadataClassname()
    {
        return 'Doctrine\\ORM\\Mapping\\ClassMetadata';
    }
}
