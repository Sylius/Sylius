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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    public function __construct(
        private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        private ObjectManager $promotionManager,
    ) {
    }

    /**
     * @When /^I archive the ("[^"]+" catalog promotion)$/
     */
    public function iArchiveTheCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setArchivedAt(new \DateTime());
        $catalogPromotion->setState(CatalogPromotionStates::STATE_INACTIVE);

        $this->promotionManager->flush();
    }

    /**
     * @Then /^(this catalog promotion) should no longer exist in the promotion registry$/
     */
    public function catalogPromotionShouldNotExistInTheRegistry(CatalogPromotionInterface $catalogPromotion): void
    {
        Assert::null($this->catalogPromotionRepository->findOneBy(['code' => $catalogPromotion->getCode()]));
    }

    /**
     * @Then /^(the catalog promotion) should still exist in the registry$/
     */
    public function catalogPromotionShouldStillExistInTheRegistry(CatalogPromotionInterface $catalogPromotion): void
    {
        Assert::notNull($this->catalogPromotionRepository->find($catalogPromotion->getId()));
    }
}
