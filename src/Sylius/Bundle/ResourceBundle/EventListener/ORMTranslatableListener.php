<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Prezent Internet B.V. <info@prezent.nl>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ORMTranslatableListener implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    private $resourceMetadataRegistry;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param RegistryInterface $resourceMetadataRegistry
     * @param ContainerInterface $container
     */
    public function __construct(
        RegistryInterface $resourceMetadataRegistry,
        ContainerInterface $container
    ) {
        $this->resourceMetadataRegistry = $resourceMetadataRegistry;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    /**
     * Add mapping to translatable entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflection = $classMetadata->reflClass;

        if (!$reflection || $reflection->isAbstract()) {
            return;
        }

        if ($reflection->implementsInterface(TranslatableInterface::class)) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflection->implementsInterface(TranslationInterface::class)) {
            $this->mapTranslation($classMetadata);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        /** @var LocaleContextInterface $localeContext */
        $localeContext = $this->container->get('sylius_resource.translation.locale_context');

        /** @var LocaleProviderInterface $localeProvider */
        $localeProvider = $this->container->get('sylius_resource.translation.locale_provider');

        try {
            $entity->setCurrentLocale($localeContext->getLocaleCode());
        } catch (LocaleNotFoundException $exception) {
            $entity->setCurrentLocale($localeProvider->getDefaultLocaleCode());
        }
        $entity->setFallbackLocale($localeProvider->getDefaultLocaleCode());
    }

    /**
     * Add mapping data to a translatable entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapTranslatable(ClassMetadata $metadata)
    {
        $className = $metadata->name;

        try {
            $resourceMetadata = $this->resourceMetadataRegistry->getByClass($className);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        if (!$resourceMetadata->hasParameter('translation')) {
            return;
        }

        /** @var MetadataInterface $translationResourceMetadata */
        $translationResourceMetadata = $this->resourceMetadataRegistry->get($resourceMetadata->getAlias().'_translation');

        $metadata->mapOneToMany([
            'fieldName' => 'translations',
            'targetEntity' => $translationResourceMetadata->getClass('model'),
            'mappedBy' => 'translatable',
            'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
            'indexBy' => 'locale',
            'cascade' => ['persist', 'merge', 'remove'],
            'orphanRemoval' => true,
        ]);
    }

    /**
     * Add mapping data to a translation entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapTranslation(ClassMetadata $metadata)
    {
        $className = $metadata->name;

        try {
            $resourceMetadata = $this->resourceMetadataRegistry->getByClass($className);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        /** @var MetadataInterface $translatableResourceMetadata */
        $translatableResourceMetadata = $this->resourceMetadataRegistry->get(str_replace('_translation', '', $resourceMetadata->getAlias()));

        $metadata->mapManyToOne([
            'fieldName' => 'translatable',
            'targetEntity' => $translatableResourceMetadata->getClass('model'),
            'inversedBy' => 'translations',
            'joinColumns' => [[
                'name' => 'translatable_id',
                'referencedColumnName' => 'id',
                'onDelete' => 'CASCADE',
                'nullable' => false,
            ]],
        ]);

        if (!$metadata->hasField('locale')) {
            $metadata->mapField([
                'fieldName' => 'locale',
                'type' => 'string',
                'nullable' => false,
            ]);
        }

        // Map unique index.
        $columns = [
            $metadata->getSingleAssociationJoinColumnName('translatable'),
            'locale',
        ];

        if (!$this->hasUniqueConstraint($metadata, $columns)) {
            $constraints = isset($metadata->table['uniqueConstraints']) ? $metadata->table['uniqueConstraints'] : [];

            $constraints[$metadata->getTableName().'_uniq_trans'] = [
                'columns' => $columns,
            ];

            $metadata->setPrimaryTable([
                'uniqueConstraints' => $constraints,
            ]);
        }
    }

    /**
     * Check if a unique constraint has been defined.
     *
     * @param ClassMetadata $metadata
     * @param array         $columns
     *
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $metadata, array $columns)
    {
        if (!isset($metadata->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($metadata->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }
}
