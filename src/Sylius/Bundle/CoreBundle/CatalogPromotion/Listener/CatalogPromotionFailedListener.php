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
use SM\Factory\FactoryInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionFailed;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionFailedListener
{
    public function __construct(
        private AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsCatalogPromotionsProcessor,
        private RepositoryInterface $catalogPromotionRepository,
        private EntityManagerInterface $entityManager,
        private FactoryInterface $stateMachine,
    ) {
    }

    public function __invoke(CatalogPromotionFailed $event): void
    {
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);
        if (null === $catalogPromotion) {
            return;
        }

        $stateMachine = $this->stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH);

        if ($stateMachine->can(CatalogPromotionTransitions::TRANSITION_PROCESS)) {
            $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_PROCESS);
        }

        $stateMachine->apply(CatalogPromotionTransitions::TRANSITION_FAIL);

        $this->allProductVariantsCatalogPromotionsProcessor->process();

        $this->entityManager->flush();
    }
}
