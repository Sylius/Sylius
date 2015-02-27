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
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Prezent Internet B.V. <info@prezent.nl>
 */
class ODMTranslatableListener implements EventSubscriber, TranslatableListenerInterface
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
        $reflection    = $classMetadata->reflClass;

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
     * Add mapping data to a translatable entity
     *
     * @param ClassMetadata $mapping
     */
    private function mapTranslatable(ClassMetadata $metadata)
    {
        // In the case A -> B -> TranslatableInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return.
        // In the case A -> B -> TranslatableInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return.
        if (!isset($this->mappings[$metadata->name])) {
            return;
        }

        $config = $this->mappings[$metadata->name];
        $mapping = $config['translation']['mapping'];

        $metadata->mapManyEmbedded(array(
            'fieldName'      => $mapping['translatable']['translations'],
            'targetDocument' => $config['translation']['model'],
            'strategy'       => 'set'
        ));
    }

    /**
     * Add mapping data to a translation entity
     *
     * @param ClassMetadata $mapping
     */
    private function mapTranslation(ClassMetadata $metadata)
    {
        // In the case A -> B -> TranslationInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return;
        if (!isset($this->mappings[$metadata->name])) {
            return;
        }

        $config = $this->mappings[$metadata->name];
        $mapping = $config['translation']['mapping'];

        $metadata->isEmbeddedDocument = true;
        $metadata->isMappedSuperclass = false;
        $metadata->setIdentifier(null);

        // Map locale field.
        if (!$metadata->hasField($mapping['translation']['locale'])) {
            $metadata->mapField(array(
                'fieldName' => $mapping['translation']['locale'],
                'type'      => 'string',
            ));
        }

        // Map unique index.
        $keys = array(
            $mapping['translation']['translatable'] => 1,
            $mapping['translation']['locale'] => 1
        );

        if (!$this->hasUniqueIndex($metadata, $keys)) {
            $metadata->addIndex($keys, array(
                'unique' => true
            ));
        }
    }

    /**
     * Load translations
     *
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        // Sometimes $document is a doctrine proxy class, we therefore need to retrieve it's real class
        $name = $args->getDocumentManager()->getClassMetadata(get_class($document))->getName();

        if (!isset($this->mappings[$name])) {
            return;
        }

        $metadata = $this->mappings[$name];

        if (isset($metadata['fallback_locale'])) {
            $setter = 'set'.ucfirst($metadata['fallback_locale']);
            $document->$setter($this->fallbackLocale);
        }
        if (isset($metadata['current_locale'])) {
            $setter = 'set'.ucfirst($metadata['current_locale']);
            $document->$setter($this->currentLocale);
        }
    }
}
