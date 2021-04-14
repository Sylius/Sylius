<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\OrderItemIdAwareInterface;

class OrderItemIdAwareInputCommandDataTransformer implements CommandDataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        $explodedUri = explode('/', $context['request_uri']);
        $object->setOrderItemId(end($explodedUri));

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof OrderItemIdAwareInterface;
    }
}
