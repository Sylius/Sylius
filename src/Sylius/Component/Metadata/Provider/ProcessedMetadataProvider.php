<?php

namespace Sylius\Component\Metadata\Provider;

use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Processor\MetadataProcessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProcessedMetadataProvider implements MetadataProviderInterface
{
    /**
     * @var MetadataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var MetadataProcessorInterface
     */
    private $metadataProcessor;

    /**
     * @param MetadataProviderInterface $metadataProvider
     * @param MetadataProcessorInterface $metadataProcessor
     */
    public function __construct(MetadataProviderInterface $metadataProvider, MetadataProcessorInterface $metadataProcessor)
    {
        $this->metadataProvider = $metadataProvider;
        $this->metadataProcessor = $metadataProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        $compiledMetadata = $this->metadataProvider->getMetadataBySubject($metadataSubject);

        if (null === $compiledMetadata) {
            return null;
        }

        return $this->metadataProcessor->process($compiledMetadata, ['subject' => $metadataSubject]);
    }
}
