<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Seo\HierarchyProvider;

use Sylius\Component\Seo\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataHierarchyProviderInterface
{
    /**
     * Returns identifiers in order from the most specific to the least specific.
     *
     * @param MetadataSubjectInterface $metadataSubject
     *
     * @return string[]
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadataSubject);

    /**
     * @param MetadataSubjectInterface $metadataSubject
     *
     * @return boolean
     */
    public function supports(MetadataSubjectInterface $metadataSubject);
}