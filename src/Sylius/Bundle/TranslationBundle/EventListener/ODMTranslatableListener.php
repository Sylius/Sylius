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
     * String Locale to use for translations
     * @var string
     */
    private $currentLocale = 'en';

    /**
     * String Locale to use when the current locale is not available
     * @var string
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
     * @param array  $metadata
     * @param string $fallbackLocale
     */
    public function __construct(array $metadata, $fallbackLocale)
    {
        $this->metadata = $metadata;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Set the current locale
     *
     * @param  string $currentLocale
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

        $mapping->mapManyEmbedded(array(
            'fieldName'      => $translatableMetadata['field'],
            'targetDocument' => $translatableMetadata['targetEntity'],
            'strategy'       => 'set'
        ));
    }

    /**
     * Add mapping data to a translation entity
     *
     * @param ClassMetadata $mapping
     */
    private function mapTranslation(ClassMetadata $mapping)
    {
        // In the case A -> B -> TranslationInterface, B might not have mapping defined as it
        // is probably defined in A, so in that case, we just return;
        if (!isset($this->metadata[$mapping->name])) {
            return;
        }

        $mapping->isEmbeddedDocument = true;
        $mapping->isMappedSuperclass = false;
        $mapping->setIdentifier(null);
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

        if (!isset($this->metadata[$name])) {
            return;
        }

        $metadata = $this->metadata[$name];

        if (isset($metadata['fallbackLocale'])) {
            $setter = 'set'.ucfirst($metadata['fallbackLocale']);
            $document->$setter($this->fallbackLocale);
        }

        if (isset($metadata['currentLocale'])) {
            $setter = 'set'.ucfirst($metadata['currentLocale']);
            $document->$setter($this->currentLocale);
        }
    }
}
