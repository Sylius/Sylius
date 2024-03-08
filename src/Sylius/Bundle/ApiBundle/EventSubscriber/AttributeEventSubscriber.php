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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AttributeEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ServiceRegistryInterface $attributeTypeRegistry)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['assignStorageType', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function assignStorageType(ViewEvent $event): void
    {
        $attribute = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            !$attribute instanceof AttributeInterface ||
            !in_array($method, [Request::METHOD_POST, Request::METHOD_PUT], true)
        ) {
            return;
        }

        if (null === $attribute->getType() || '' === $attribute->getType()) {
            return;
        }

        if (
            null !== $attribute->getStorageType() ||
            !$this->attributeTypeRegistry->has($attribute->getType())
        ) {
            return;
        }

        /** @var AttributeTypeInterface $attributeType */
        $attributeType = $this->attributeTypeRegistry->get($attribute->getType());

        $attribute->setStorageType($attributeType->getStorageType());

        $event->setControllerResult($attribute);
    }
}
