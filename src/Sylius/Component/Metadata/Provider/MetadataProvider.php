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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Metadata\Compiler\MetadataCompilerInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\RootMetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProvider implements MetadataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $rootMetadataRepository;

    /**
     * @var MetadataCompilerInterface
     */
    protected $metadataCompiler;

    /**
     * @var MetadataHierarchyProviderInterface
     */
    protected $metadataHierarchyProvider;

    /**
     * @param RepositoryInterface $rootMetadataRepository
     * @param MetadataCompilerInterface $metadataCompiler
     * @param MetadataHierarchyProviderInterface $metadataHierarchyProvider
     */
    public function __construct(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider
    ) {
        $this->rootMetadataRepository = $rootMetadataRepository;
        $this->metadataCompiler = $metadataCompiler;
        $this->metadataHierarchyProvider = $metadataHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        $identifiers = $this->getHierarchyByMetadataSubject($metadataSubject);


        $parents = [];
        $baseMetadata = null;
        foreach ($identifiers as $identifier) {
            /** @var RootMetadataInterface $rootMetadata */
            $rootMetadata = $this->rootMetadataRepository->findOneBy(['key' => $identifier]);

            if (null === $rootMetadata) {
                continue;
            }

            if (null === $baseMetadata) {
                $baseMetadata = $rootMetadata->getMetadata();

                continue;
            }

            $parents[] = $rootMetadata->getMetadata();
        }

        if (null === $baseMetadata) {
            return null;
        }

        return $this->metadataCompiler->compile($baseMetadata, $parents);
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     *
     * @return string[]
     */
    private function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadataSubject)
    {
        if ($this->metadataHierarchyProvider->supports($metadataSubject)) {
            $identifiers = $this->metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject);
        } else {
            $identifiers = [
                $metadataSubject->getMetadataIdentifier(),
                $metadataSubject->getMetadataClassIdentifier(),
            ];
        }

        return $identifiers;
    }
}
