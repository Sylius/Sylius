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

namespace Sylius\Bundle\ApiBundle\Application\Resource;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

/** @implements ProviderInterface<SampleProduct> */
final readonly class SampleProductProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object
    {
        if ($operation instanceof CollectionOperationInterface) {
            return [new SpecialSampleProduct(1, 'Special Product')];
        }

        return new SpecialSampleProduct((int) ($uriVariables['id'] ?? 1), 'Special Product');
    }
}
