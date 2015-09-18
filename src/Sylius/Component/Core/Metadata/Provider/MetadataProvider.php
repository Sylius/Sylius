<?php

namespace Sylius\Component\Core\Metadata\Provider;

use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\Custom\PageMetadata;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Provider\MetadataProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProvider implements MetadataProviderInterface
{
    /**
     * @var MetadataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var MetadataHierarchyProviderInterface
     */
    private $metadataHierarchyProvider;

    /**
     * @param MetadataProviderInterface $metadataProvider
     * @param MetadataHierarchyProviderInterface $metadataHierarchyProvider
     */
    public function __construct(MetadataProviderInterface $metadataProvider, MetadataHierarchyProviderInterface $metadataHierarchyProvider)
    {
        $this->metadataProvider = $metadataProvider;
        $this->metadataHierarchyProvider = $metadataHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        $metadata = $this->metadataProvider->getMetadataBySubject($metadataSubject);
        if (null !== $metadata) {
            return $metadata;
        }

        $hierarchy = $this->metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject);
        if ('DefaultPage' === end($hierarchy)) {
            return new PageMetadata();
        }

        return null;
    }
}