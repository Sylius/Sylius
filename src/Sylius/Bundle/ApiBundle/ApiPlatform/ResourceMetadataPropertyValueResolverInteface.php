<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\ApiPlatform;

use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

interface ResourceMetadataPropertyValueResolverInteface
{
    /**
     * @return mixed
     */
    public function resolve(string $propertyName, ResourceMetadata $parentResourceMetadata, array $childResourceMetadata);
}
