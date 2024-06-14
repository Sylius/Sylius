<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Converter;

use ApiPlatform\Metadata\IriConverterInterface as BaseIriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\UrlGeneratorInterface;

interface IriConverterInterface extends BaseIriConverterInterface
{
    public function getIriFromResourceInSection(
        object|string $resource,
        string $section,
        int $referenceType = UrlGeneratorInterface::ABS_PATH,
        ?Operation $operation = null,
        array $context = [],
    ): ?string;
}
