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
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionEnded;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionStateChangedListener
{
    public function __construct(
        private CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        private RepositoryInterface $catalogPromotionRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(CatalogPromotionCreated|CatalogPromotionUpdated|CatalogPromotionEnded $event): void
    {
        /** @var CatalogPromotionInterface|null $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionRepository->findOneBy(['code' => $event->code]);
        if (null === $catalogPromotion) {
            return;
        }

        $this->catalogPromotionStateProcessor->process($catalogPromotion);

        $this->entityManager->flush();
    }
}
