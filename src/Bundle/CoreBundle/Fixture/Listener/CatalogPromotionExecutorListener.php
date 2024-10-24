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

namespace Sylius\Bundle\CoreBundle\Fixture\Listener;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture;
use Sylius\Bundle\FixturesBundle\Listener\AbstractListener;
use Sylius\Bundle\FixturesBundle\Listener\AfterFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionExecutorListener extends AbstractListener implements AfterFixtureListenerInterface
{
    public function __construct(
        private AllProductVariantsCatalogPromotionsProcessorInterface $allCatalogPromotionsProcessor,
        private CatalogPromotionRepositoryInterface $catalogPromotionsRepository,
        private MessageBusInterface $messageBus,
        private iterable $defaultCriteria = [],
    ) {
    }

    public function afterFixture(FixtureEvent $fixtureEvent, array $options): void
    {
        if (!$fixtureEvent->fixture() instanceof CatalogPromotionFixture) {
            return;
        }

        $this->allCatalogPromotionsProcessor->process();

        $catalogPromotions = $this->catalogPromotionsRepository->findByCriteria($this->defaultCriteria);

        /** @var CatalogPromotionInterface $catalogPromotion */
        foreach ($catalogPromotions as $catalogPromotion) {
            // process
            $this->messageBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()));

            // activate/deactivate
            $this->messageBus->dispatch(new UpdateCatalogPromotionState($catalogPromotion->getCode()));
        }
    }

    public function getName(): string
    {
        return 'catalog_promotion_processor_executor';
    }
}
