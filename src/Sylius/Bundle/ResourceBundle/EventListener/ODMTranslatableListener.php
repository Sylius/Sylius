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
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

@trigger_error(sprintf('The "%s" class is deprecated since Sylius 1.3. Doctrine MongoDB and PHPCR support will no longer be supported in Sylius 2.0.', ODMTranslatableListener::class), \E_USER_DEPRECATED);

final class ODMTranslatableListener implements EventSubscriber
{
    /** @var string */
    private $currentLocale;

    /** @var string */
    private $fallbackLocale;

    /** @var array */
    private $mappings;

    /**
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
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    /**
     * Add mapping to translatable entities
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
     * Add mapping data to a translatable entity
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

        $metadata->mapManyEmbedded([
            'fieldName' => $mapping['translatable']['translations'],
            'targetDocument' => $config['translation']['model'],
            'strategy' => 'set',
        ]);
    }

    /**
     * Add mapping data to a translation entity
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
            $metadata->mapField([
                'fieldName' => $mapping['translation']['locale'],
                'type' => 'string',
            ]);
        }

        // Map unique index.
        $keys = [
            $mapping['translation']['translatable'] => 1,
            $mapping['translation']['locale'] => 1,
        ];

        if (!$this->hasUniqueIndex($metadata, $keys)) {
            $metadata->addIndex($keys, [
                'unique' => true,
            ]);
        }
    }

    /**
     * Load translations
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
            $setter = 'set' . ucfirst($metadata['fallback_locale']);
            $document->$setter($this->fallbackLocale);
        }
        if (isset($metadata['current_locale'])) {
            $setter = 'set' . ucfirst($metadata['current_locale']);
            $document->$setter($this->currentLocale);
        }
    }
}
