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
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private PromotionRepositoryInterface $promotionRepository,
        private ObjectManager $promotionManager,
    ) {
    }

    /**
     * @When /^I delete a ("([^"]+)" promotion)$/
     */
    public function iDeletePromotion(PromotionInterface $promotion)
    {
        $this->promotionRepository->remove($promotion);
    }

    /**
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iTryToDeletePromotion(PromotionInterface $promotion)
    {
        try {
            $this->promotionRepository->remove($promotion);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When I archive the :promotion promotion
     */
    public function iArchiveThePromotion(PromotionInterface $promotion): void
    {
        $promotion->setArchivedAt(new \DateTime());

        $this->promotionManager->flush();
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion)
    {
        Assert::null($this->promotionRepository->findOneBy(['code' => $promotion->getCode()]));
    }

    /**
     * @Then promotion :promotion should still exist in the registry
     */
    public function promotionShouldStillExistInTheRegistry(PromotionInterface $promotion)
    {
        Assert::notNull($this->promotionRepository->find($promotion->getId()));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), ForeignKeyConstraintViolationException::class);
    }

    /**
     * @Then the promotion :promotion should still exist in the registry
     */
    public function thePromotionShouldStillExistInTheRegistry(PromotionInterface $promotion): void
    {
        Assert::notNull($this->promotionRepository->find($promotion));
    }
}
