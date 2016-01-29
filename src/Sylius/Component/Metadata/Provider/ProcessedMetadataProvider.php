<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function findMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        $compiledMetadata = $this->metadataProvider->findMetadataBySubject($metadataSubject);

        if (null === $compiledMetadata) {
            return null;
        }

        return $this->metadataProcessor->process($compiledMetadata, ['subject' => $metadataSubject]);
    }
}
