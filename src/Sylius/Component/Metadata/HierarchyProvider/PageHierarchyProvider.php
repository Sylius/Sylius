<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      24/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\HierarchyProvider;

use Sylius\Component\Metadata\Model\Custom\PageInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * This acts as a dummy provider to retrieve a fully processed set of DefaultPage metadata
 * without having to break up the existing structure around Accessor -> Provider -> Processor
 *
 * @author Pete Ward <peter.ward@reiss.com>
 */
class PageHierarchyProvider implements MetadataHierarchyProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadataSubject)
    {
        return [PageInterface::METADATA_CLASS_IDENTIFIER];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataSubjectInterface $metadataSubject)
    {
        return $metadataSubject instanceof PageInterface;
    }
}