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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

/** @internal */
final readonly class ForceEagerAwareResourceMetadataCollectionFactory implements ResourceMetadataCollectionFactoryInterface
{
    private const SYLIUS_OPERATION_PREFIX = 'sylius_api_';

    public function __construct(
        private ResourceMetadataCollectionFactoryInterface $decorated,
        private bool $forceEager,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            if (null === $operations) {
                continue;
            }

            /** @var Operation $operation */
            foreach ($operations as $name => $operation) {
                if (!$this->isApplicable($operation)) {
                    continue;
                }

                $operations->add($name, $operation->withForceEager($this->forceEager));
            }
        }

        return $resourceMetadataCollection;
    }

    private function isApplicable(Operation $operation): bool
    {
        if (!$operation instanceof Get && !$operation instanceof GetCollection) {
            return false;
        }

        if (null !== $operation->getForceEager()) {
            return false;
        }

        return str_starts_with((string) $operation->getName(), self::SYLIUS_OPERATION_PREFIX);
    }
}
