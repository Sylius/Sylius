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

namespace Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\HttpOperation;
use Sylius\Resource\Model\ResourceInterface;

/** @template T of ResourceInterface */
interface UriTemplateParentResourceResolverInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return T
     */
    public function resolve(ResourceInterface $item, HttpOperation $operation, array $context = []): ResourceInterface;
}
