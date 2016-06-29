<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Metadata\HierarchyProvider;

use Sylius\Core\Model\ArchetypeInterface;
use Sylius\Core\Model\ProductInterface;
use Sylius\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductHierarchyProvider implements MetadataHierarchyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $product)
    {
        $hierarchy = [
            $product->getMetadataIdentifier(),
        ];

        /** @var ArchetypeInterface $productArchetype */
        $productArchetype = $product->getArchetype();
        if (null !== $productArchetype) {
            $hierarchy[] = $productArchetype->getMetadataIdentifier();
            
            while ($productArchetype = $productArchetype->getParent()) {
                $hierarchy[] = $productArchetype->getMetadataIdentifier();
            }
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
