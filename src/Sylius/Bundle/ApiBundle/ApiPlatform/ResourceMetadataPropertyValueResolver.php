<?php

namespace Sylius\Bundle\ApiBundle\ApiPlatform;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

class ResourceMetadataPropertyValueResolver
{
    /** @var ConfigMergeManager */
    private $configMergeManager;

    public function __construct(ConfigMergeManager $configMergeManager)
    {
        $this->configMergeManager = $configMergeManager;
    }

    /**
     * @return mixed
     */
    public function resolve(
        string $propertyName,
        ResourceMetadata $parentResourceMetadata,
        array $childResourceMetadata
    ) {
        $parentPropertyValue = $parentResourceMetadata->{'get' . ucfirst($propertyName)}();

        $childPropertyValue = $childResourceMetadata[$propertyName];

        if (null === $childPropertyValue) {
            return $parentPropertyValue;
        }

        if (null === $parentPropertyValue) {
            return $childPropertyValue;
        }

        if (is_array($parentPropertyValue)) {
            if (!is_array($childPropertyValue)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid child property value type for property "%s", expected array',
                    $propertyName,
                ));
            }

            return $this->configMergeManager->mergeConfigs($parentPropertyValue, $childPropertyValue);
        }

        return $childPropertyValue;
    }
}
