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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionStateChangedListener
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(CatalogPromotionCreated|CatalogPromotionEnded|CatalogPromotionUpdated $event): void
    {
        $this->messageBus->dispatch(new UpdateCatalogPromotionState($event->code));
    }
}
