<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ManagingPromotionsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionRepositoryInterface $promotionRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->promotionRepository = $promotionRepository;
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
}
