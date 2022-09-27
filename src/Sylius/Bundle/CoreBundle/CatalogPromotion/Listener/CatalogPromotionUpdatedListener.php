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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionUpdatedListener
{
    public function __construct(
        private AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        private RepositoryInterface $catalogPromotionRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(CatalogPromotionUpdated $event): void
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);

        if (null === $catalogPromotion) {
            return;
        }

        $this->messageBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()));
        $this->allProductVariantsCatalogPromotionsProcessor->process();

        $this->entityManager->flush();
    }
}
