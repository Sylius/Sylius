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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/** @experimental */
final class SubresourceIdAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function transform($object, string $to, array $context = [])
    {
        if (null !== $object->getSubresourceId()) {
            return $object;
        }

        $attributes = $this->requestStack->getCurrentRequest()->attributes;

        $attributeKey = $object->getSubresourceIdAttributeKey();

        //Todo : To discuss - support getting attributes from url ?
        // How does it exactly work for changing item quantity

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
