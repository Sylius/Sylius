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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class PromotionContext implements Context
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
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iTryToDeletePromotion(PromotionInterface $promotion)
    {
        try {
            $this->promotionRepository->remove($promotion);

            throw new \Exception(sprintf('Promotion "%s" has been removed, but it should not.', $promotion->getName()));
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When /^I delete a ("([^"]+)" promotion)$/
     */
    public function iDeletePromotion(PromotionInterface $promotion)
    {
        $this->sharedStorage->set('promotion', $promotion);
        $this->promotionRepository->remove($promotion);
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion)
    {
        expect($this->promotionRepository->findOneBy(['code' => $promotion->getCode()]))->toBe(null);
    }

    /**
     * @Then promotion :promotion should still exist in the registry
     */
    public function promotionShouldStillExistInTheRegistry(PromotionInterface $promotion)
    {
        expect($this->promotionRepository->find($promotion->getId()))->toNotBe(null);
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        expect($this->sharedStorage->get('last_exception'))
            ->toBeAnInstanceOf(ForeignKeyConstraintViolationException::class)
        ;
    }
}
