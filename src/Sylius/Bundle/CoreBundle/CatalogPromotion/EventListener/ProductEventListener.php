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

use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Event\ProductUpdated;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class ProductEventListener
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function dispatchProductCreatedEvent(GenericEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $this->eventBus->dispatch(new ProductCreated($product->getCode()));
    }

    public function dispatchProductUpdatedEvent(GenericEvent $event): void
    {
        $product = $event->getSubject();
        Assert::isInstanceOf($product, ProductInterface::class);

        $this->eventBus->dispatch(new ProductUpdated($product->getCode()));
    }
}
