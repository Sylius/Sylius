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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionRemovalAnnouncer implements CatalogPromotionRemovalAnnouncerInterface
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function dispatchCatalogPromotionRemoval(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->commandBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()));

        if ($catalogPromotion->isEnabled()) {
            $this->commandBus->dispatch(new DisableCatalogPromotion($catalogPromotion->getCode()));
        }

        $this->commandBus->dispatch(new RemoveCatalogPromotion($catalogPromotion->getCode()));
    }
}
