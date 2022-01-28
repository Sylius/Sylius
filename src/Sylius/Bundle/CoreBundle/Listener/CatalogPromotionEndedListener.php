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

namespace Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Processor\AllCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionEndedListener
{
    public function __construct(
        private AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        private RepositoryInterface $catalogPromotionRepository,
        private EntityManagerInterface $entityManager,
        private FactoryInterface $stateMachine,
    ) {
    }

    public function __invoke(CatalogPromotionEnded $event): void
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);

        if (null === $catalogPromotion) {
            return;
        }

        $this->allProductVariantsCatalogPromotionsProcessor->process();

        $this->catalogPromotionStateProcessor->process($catalogPromotion);

        $this->entityManager->flush();
    }
}
