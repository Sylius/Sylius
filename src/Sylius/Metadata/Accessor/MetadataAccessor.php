<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Metadata\Accessor;

use Sylius\Metadata\Model\MetadataSubjectInterface;
use Sylius\Metadata\Provider\MetadataProviderInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class MetadataAccessor implements MetadataAccessorInterface
{
    /**
     * @var MetadataProviderInterface
     */
    private $metadataProvider;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param MetadataProviderInterface $metadataProvider
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(MetadataProviderInterface $metadataProvider, PropertyAccessorInterface $propertyAccessor)
    {
        $this->metadataProvider = $metadataProvider;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $propertyPath = null)
    {
        $metadata = $this->metadataProvider->findMetadataBySubject($metadataSubject);

        if (null === $propertyPath) {
            return $metadata;
        }

        return $this->propertyAccessor->getValue($metadata, $propertyPath);
    }
}
