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
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Prezent Internet B.V. <info@prezent.nl>
 */
class TranslatableListener implements EventSubscriber
{
    /**
     * String Locale to use for translations
     * @var
     */
    private $currentLocale = 'en';

    /**
     * String Locale to use when the current locale is not available
     * @var
     */
    private $fallbackLocale = 'en';

    /**
     * Array containing translation entities metadata
     * @var array
     */
    private $metadata;

    /**
     * Constructor
     *
     * @param array $metadata
     * @param string $fallbackLocale
     *
     */
    public function __construct(array $metadata, $fallbackLocale)
    {
        $this->metadata = $metadata;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Set the current locale
     *
     * @param string $currentLocale
     * @return self
     */
    public function setCurrentLocale($currentLocale)
    {
        $this->currentLocale = $currentLocale;
        return $this;
    }

    /**
     * Get the fallback locale
     *
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
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
     * @return void
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $reflClass     = $classMetadata->reflClass;

        if (!$reflClass || $reflClass->isAbstract()) {
            return;
        }

        if ($reflClass->implementsInterface('Sylius\Component\Translation\Model\TranslatableInterface')) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflClass->implementsInterface('Sylius\Component\Translation\Model\TranslationInterface')) {
            $this->mapTranslation($classMetadata);
        }
    }

    /**
     * Add mapping data to a translatable entity
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    private function mapTranslatable(ClassMetadata $mapping)
    {
        // In the case A -> B -> TranslatableInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return;
        if (!isset($this->metadata[$mapping->name])) {
            return;
        }

        $translatableMetadata = $this->metadata[$mapping->name];

        $translationMetadata = $this->metadata[$translatableMetadata['targetEntity']];

        $mapping->mapOneToMany(array(
            'fieldName'     => $translatableMetadata['field'],
            'targetEntity'  => $translatableMetadata['targetEntity'],
            'mappedBy'      => $translationMetadata['field'],
            'fetch'         => ClassMetadataInfo::FETCH_EXTRA_LAZY,
            'indexBy'       => $translationMetadata['locale'],
            'cascade'       => array('persist', 'merge', 'remove'),
            'orphanRemoval' => true,
        ));
    }

    /**
     * Add mapping data to a translation entity
     *
     * @param ClassMetadata $mapping
     * @return void
     */
    private function mapTranslation(ClassMetadata $mapping)
    {
        // In the case A -> B -> TranslationInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return;
        if (!isset($this->metadata[$mapping->name])) {
            return;
        }

        $translationMetadata = $this->metadata[$mapping->name];

        // Map translatable relation
        $translatableMetadata = $this->metadata[$translationMetadata['targetEntity']];

        $mapping->mapManyToOne(array(
            'fieldName'    => $translationMetadata['field'],
            'targetEntity' => $translationMetadata['targetEntity'],
            'inversedBy'   => $translatableMetadata['field'],
            'joinColumns'  => array(array(
                'name'                 => 'translatable_id',
                'referencedColumnName' => 'id',
                'onDelete'             => 'CASCADE',
                'nullable'             => false,
            )),
        ));

        // Map locale field
        if (!$mapping->hasField($translationMetadata['locale'])) {
            $mapping->mapField(array(
                'fieldName' => $translationMetadata['locale'],
                'type' => 'string',
            ));
        }

        // Map unique index
        $columns = array(
            $mapping->getSingleAssociationJoinColumnName($translationMetadata['field']),
                $translationMetadata['locale'],
        );

        if (!$this->hasUniqueConstraint($mapping, $columns)) {
            $constraints = isset($mapping->table['uniqueConstraints']) ? $mapping->table['uniqueConstraints'] : array();
            $constraints[$mapping->getTableName() . '_uniq_trans'] = array(
                'columns' => $columns,
            );

            $mapping->setPrimaryTable(array(
                'uniqueConstraints' => $constraints,
            ));
        }
    }

    /**
     * Check if an unique constraint has been defined
     *
     * @param ClassMetadata $mapping
     * @param array $columns
     * @return bool
     */
    private function hasUniqueConstraint(ClassMetadata $mapping, array $columns)
    {
        if (!isset($mapping->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($mapping->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load translations
     *
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // Sometimes $entity is a doctrine proxy class, we therefore need to retrieve it's real class
        $name = $args->getEntityManager()->getClassMetadata(get_class($entity))->getName();

        if (!isset($this->metadata[$name])) {
            return;
        }

        $metadata = $this->metadata[$name];

        if (isset($metadata['fallbackLocale'])) {
            $setter = 'set'. ucfirst($metadata['fallbackLocale']);
            $entity->$setter($this->fallbackLocale);
        }

        if (isset($metadata['currentLocale'])) {
            $setter = 'set'. ucfirst($metadata['currentLocale']);
            $entity->$setter($this->currentLocale);
        }
    }
}
