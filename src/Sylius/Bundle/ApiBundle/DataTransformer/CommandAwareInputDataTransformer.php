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

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Command\SubresourceIdAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class CommandAwareInputDataTransformer implements DataTransformerInterface
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function transform($object, string $to, array $context = [])
    {
        if ($object instanceof OrderTokenValueAwareInterface) {
            $object = $this->assignOrderTokenValue($object, $to, $context);
        }

        if ($object instanceof SubresourceIdAwareInterface) {
            $object = $this->assignSubresourceId($object, $to, $context);
        }

        return $object;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return is_a($context['input']['class'], CommandAwareDataTransformerInterface::class, true);
    }

    private function assignOrderTokenValue(OrderTokenValueAwareInterface $object, string $to, array $context = [])
    {
        /** @var OrderInterface $cart */
        $cart = $context['object_to_populate'];

        $object->setOrderTokenValue($cart->getTokenValue());

        return $object;
    }

    private function assignSubresourceId(SubresourceIdAwareInterface $object, string $to, array $context = [])
    {
        $attributes = $this->requestStack->getCurrentRequest()->attributes->all();

        /** @var string|null $subresourceId */
        $subresourceId = $attributes[$object->getSubresourceIdAttributeKey()] ?? null;

        Assert::notNull($subresourceId, 'Path does not have subresource id');

        $object->setSubresourceId($subresourceId);

        return $object;
    }
}
