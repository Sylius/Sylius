<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Metadata\HierarchyProvider;

use Sylius\Component\Core\Model\ArchetypeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductHierarchyProvider implements MetadataHierarchyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadataSubject)
    {
        /** @var ProductInterface $product */
        $product = $metadataSubject;

        $hierarchy = [
            $product->getMetadataIdentifier(),
        ];

        /** @var ArchetypeInterface $productArchetype */
        $productArchetype = $product->getArchetype();
        if (null !== $productArchetype) {
            $hierarchy[] = $productArchetype->getMetadataIdentifier();
        }

        $hierarchy[] = $product->getMetadataClassIdentifier();

        $hierarchy[] = 'DefaultPage';

        return $hierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataSubjectInterface $metadataSubject)
    {
        return $metadataSubject instanceof ProductInterface;
    }
}
