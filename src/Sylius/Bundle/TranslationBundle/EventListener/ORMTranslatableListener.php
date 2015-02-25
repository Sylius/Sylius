<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TranslationBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sylius\Component\Translation\Model\TranslatableInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Prezent Internet B.V. <info@prezent.nl>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ORMTranslatableListener implements EventSubscriber, TranslatableListenerInterface
{
    /**
     * Locale to use for translations.
     *
     * @var string
     */
    private $currentLocale;

    /**
     * Locale to use when the current locale is not available.
     *
     * @var string
     */
    private $fallbackLocale;

    /**
     * Mapping.
     *
     * @var array
     */
    private $mappings;

    /**
     * @param string $mappings
     * @param string $fallbackLocale
     */
    public function __construct(array $mappings, $fallbackLocale)
    {
        $this->mappings = $mappings;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::loadClassMetadata,
            Events::postLoad,
        );
    }

    /**
     * Add mapping to translatable entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflection     = $classMetadata->reflClass;

        if (!$reflection || $reflection->isAbstract()) {
            return;
        }

        if ($reflection->implementsInterface('Sylius\Component\Translation\Model\TranslatableInterface')) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflection->implementsInterface('Sylius\Component\Translation\Model\TranslationInterface')) {
            $this->mapTranslation($classMetadata);
        }

    }

    /**
     * Add mapping data to a translatable entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapTranslatable(ClassMetadata $metadata)
    {
        // In the case A -> B -> TranslatableInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return.
        if (!isset($this->mappings[$metadata->name])) {
            return;
        }

        $config = $this->mappings[$metadata->name];
        $mapping = $config['translation']['mapping'];

        $metadata->mapOneToMany(array(
            'fieldName'     => $mapping['translatable']['translations'],
            'targetEntity'  => $config['translation']['model'],
            'mappedBy'      => $mapping['translation']['translatable'],
            'fetch'         => ClassMetadataInfo::FETCH_EXTRA_LAZY,
            'indexBy'       => $mapping['translation']['locale'],
            'cascade'       => array('persist', 'merge', 'remove'),
            'orphanRemoval' => true,
        ));
    }

    /**
     * Add mapping data to a translation entity.
     *
     * @param ClassMetadata $metadata
     */
    private function mapTranslation(ClassMetadata $metadata)
    {
        // In the case A -> B -> TranslationInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return.
        if (!isset($this->mappings[$metadata->name])) {
            return;
        }

        $config = $this->mappings[$metadata->name];
        $mapping = $config['translation']['mapping'];

        $metadata->mapManyToOne(array(
            'fieldName'    => $mapping['translation']['translatable'],
            'targetEntity' => $config['model'],
            'inversedBy'   => $mapping['translatable']['translations'],
            'joinColumns'  => array(array(
                'name'                 => 'translatable_id',
                'referencedColumnName' => 'id',
                'onDelete'             => 'CASCADE',
                'nullable'             => false,
            )),
        ));

        if (!$metadata->hasField($mapping['translation']['locale'])) {
            $metadata->mapField(array(
                'fieldName' => $mapping['translation']['locale'],
                'type'      => 'string',
                'nullable'  => false,
            ));
        }

        // Map unique index.
        $columns = array(
            $metadata->getSingleAssociationJoinColumnName($mapping['translation']['translatable']),
            $mapping['translation']['locale'],
        );

        if (!$this->hasUniqueConstraint($metadata, $columns)) {
            $constraints = isset($metadata->table['uniqueConstraints']) ? $metadata->table['uniqueConstraints'] : array();

            $constraints[$metadata->getTableName().'_uniq_trans'] = array(
                'columns' => $columns,
            );

            $metadata->setPrimaryTable(array(
                'uniqueConstraints' => $constraints,
            ));
        }
    }

    /**
     * Check if an unique constraint has been defined.
     *
     * @param ClassMetadata $metdata
     * @param array         $columns
     *
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $metdata, array $columns)
    {
        if (!isset($metdata->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($metdata->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load translations.
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        // Sometimes $entity is a doctrine proxy class, we therefore need to retrieve it's real class.
        $classMetadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

        if (!isset($this->mappings[$classMetadata->getName()]) && !isset($this->mappings[$classMetadata->rootEntityName])) {
            return;
        }

        $mapping = isset($this->mappings[$classMetadata->name]) ? $this->mappings[$classMetadata->getName()]['translation']['mapping'] : $this->mappings[$classMetadata->rootEntityName]['translation']['mapping'];

        if (isset($mapping['translatable']['fallback_locale'])) {
            $setter = 'set'.ucfirst($mapping['translatable']['fallback_locale']);
            $entity->$setter($this->fallbackLocale);
        }

        if (isset($mapping['translatable']['current_locale'])) {
            $setter = 'set'.ucfirst($mapping['translatable']['current_locale']);
            $entity->$setter($this->currentLocale);
        }
    }
}
