<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Metadata\Provider;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Metadata\Compiler\MetadataCompilerInterface;
use Sylius\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Metadata\Model\MetadataContainerInterface;
use Sylius\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MetadataProvider implements MetadataProviderInterface
{
    /**
     * @var ObjectRepository
     */
    private $metadataContainerRepository;

    /**
     * @var MetadataCompilerInterface
     */
    private $metadataCompiler;

    /**
     * @var MetadataHierarchyProviderInterface
     */
    private $metadataHierarchyProvider;

    /**
     * @param ObjectRepository $metadataContainerRepository
     * @param MetadataCompilerInterface $metadataCompiler
     * @param MetadataHierarchyProviderInterface $metadataHierarchyProvider
     */
    public function __construct(
        ObjectRepository $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider
    ) {
        $this->metadataContainerRepository = $metadataContainerRepository;
        $this->metadataCompiler = $metadataCompiler;
        $this->metadataHierarchyProvider = $metadataHierarchyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function findMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        $identifiers = $this->metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject);

        $parents = [];
        $baseMetadata = null;
        foreach ($identifiers as $identifier) {
            /** @var MetadataContainerInterface $metadataContainer */
            // TODO: Use find($identifier) after Resource refactoring (PR #2255)
            $metadataContainer = $this->metadataContainerRepository->findOneBy(['id' => $identifier]);

            if (null === $metadataContainer) {
                continue;
            }

            if (null === $baseMetadata) {
                $baseMetadata = $metadataContainer->getMetadata();

                continue;
            }

            $parents[] = $metadataContainer->getMetadata();
        }

        if (null === $baseMetadata) {
            return null;
        }

        return $this->metadataCompiler->compile($baseMetadata, $parents);
    }
}
