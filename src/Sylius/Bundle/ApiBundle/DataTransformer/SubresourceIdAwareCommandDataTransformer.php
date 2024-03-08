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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class SubresourceIdAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function transform($object, string $to, array $context = [])
    {
        $attributes = $this->requestStack->getCurrentRequest()->attributes;

        $attributeKey = $object->getSubresourceIdAttributeKey();
        Assert::true($attributes->has($attributeKey), 'Path does not have subresource id');

        /** @var string $subresourceId */
        $subresourceId = $attributes->get($object->getSubresourceIdAttributeKey());

        $object->setSubresourceId($subresourceId);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof SubresourceIdAwareInterface;
    }
}
