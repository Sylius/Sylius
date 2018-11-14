<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ORMTranslatableListener implements EventSubscriber
{
    /** @var RegistryInterface */
    private $resourceMetadataRegistry;

    /** @var TranslatableEntityLocaleAssignerInterface */
    private $translatableEntityLocaleAssigner;

    public function __construct(
        RegistryInterface $resourceMetadataRegistry,
        object $translatableEntityLocaleAssigner
    ) {
        $this->resourceMetadataRegistry = $resourceMetadataRegistry;
        $this->translatableEntityLocaleAssigner = $this->processTranslatableEntityLocaleAssigner($translatableEntityLocaleAssigner);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    /**
     * Add mapping to translatable entities
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
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

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        $this->translatableEntityLocaleAssigner->assignLocale($entity);
    }

    /**
     * Add mapping data to a translatable entity.
     */
    private function mapTranslatable(ClassMetadata $metadata): void
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
        $translationResourceMetadata = $this->resourceMetadataRegistry->get($resourceMetadata->getAlias() . '_translation');

        if (!$metadata->hasAssociation('translations')) {
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
    }

    /**
     * Add mapping data to a translation entity.
     */
    private function mapTranslation(ClassMetadata $metadata): void
    {
        $className = $metadata->name;

        try {
            $resourceMetadata = $this->resourceMetadataRegistry->getByClass($className);
        } catch (\InvalidArgumentException $exception) {
            return;
        }

        /** @var MetadataInterface $translatableResourceMetadata */
        $translatableResourceMetadata = $this->resourceMetadataRegistry->get(str_replace('_translation', '', $resourceMetadata->getAlias()));

        if (!$metadata->hasAssociation('translatable')) {
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
        }

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
            $constraints = $metadata->table['uniqueConstraints'] ?? [];

            $constraints[$metadata->getTableName() . '_uniq_trans'] = [
                'columns' => $columns,
            ];

            $metadata->setPrimaryTable([
                'uniqueConstraints' => $constraints,
            ]);
        }
    }

    /**
     * Check if a unique constraint has been defined.
     */
    private function hasUniqueConstraint(ClassMetadata $metadata, array $columns): bool
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

    /**
     * @param object $translatableEntityLocaleAssigner
     *
     * @return TranslatableEntityLocaleAssignerInterface
     */
    private function processTranslatableEntityLocaleAssigner(object $translatableEntityLocaleAssigner): TranslatableEntityLocaleAssignerInterface
    {
        if ($translatableEntityLocaleAssigner instanceof ContainerInterface) {
            @trigger_error(
                sprintf('Passing an instance of "%s" is deprecated since 1.4. Use "%s" instead.',
                    ContainerInterface::class, TranslatableEntityLocaleAssignerInterface::class), E_USER_DEPRECATED
            );
            $translatableEntityLocaleAssigner = $translatableEntityLocaleAssigner->get('sylius.translatable_entity_locale_assigner');
        }

        if (!$translatableEntityLocaleAssigner instanceof TranslatableEntityLocaleAssignerInterface) {
            throw new \InvalidArgumentException(sprintf(
                '`$translatableEntityLocaleAssigner` was expected to return an instance of "%s" , "%s" found',
                TranslatableEntityLocaleAssignerInterface::class,
                get_class($translatableEntityLocaleAssigner)
            ));
        }

        return $translatableEntityLocaleAssigner;
    }
}
