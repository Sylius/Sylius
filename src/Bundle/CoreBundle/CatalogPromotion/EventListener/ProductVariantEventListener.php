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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener;

use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ProductVariantEventListener
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function dispatchProductVariantCreatedEvent(GenericEvent $event): void
    {
        $variant = $event->getSubject();
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $this->eventBus->dispatch(new ProductVariantCreated($variant->getCode()));
    }

    public function dispatchProductVariantUpdatedEvent(GenericEvent $event): void
    {
        $variant = $event->getSubject();
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $this->eventBus->dispatch(new ProductVariantUpdated($variant->getCode()));
    }
}
