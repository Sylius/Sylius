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
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
     * @var RepositoryInterface
     */
    private $couponRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PromotionRepositoryInterface $promotionRepository
     * @param RepositoryInterface $couponRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PromotionRepositoryInterface $promotionRepository,
        RepositoryInterface $couponRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->promotionRepository = $promotionRepository;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @When /^I try to delete ("([^"]+)" coupon)$/
     */
    public function iTryToDeleteCoupon(CouponInterface $coupon)
    {
        try {
            $this->couponRepository->remove($coupon);

            throw new \Exception(sprintf('Coupon "%s" has been removed, but it should not.', $coupon->getCode()));
        } catch(ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When /^I delete ("([^"]+)" coupon)$/
     */
    public function iDeleteCoupon(CouponInterface $coupon)
    {
        $this->sharedStorage->set('coupon_id', $coupon->getId());
        $this->couponRepository->remove($coupon);
    }

    /**
     * @Then /^([^"]+ should no longer) exist in the registry$/
     */
    public function couponShouldNotExistInTheRegistry($couponId)
    {
        expect($this->couponRepository->find($couponId))->toBe(null);
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

    /**
     * @Then /^([^"]+) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(CouponInterface $coupon)
    {
        expect($this->couponRepository->find($coupon->getId()))->toNotBe(null);
    }

    /**
     * @When /^I delete (promotion "([^"]+)")$/
     */
    public function iDeletePromotion(PromotionInterface $promotion)
    {
        try {
            $this->promotionRepository->remove($promotion);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('exception', $exception);
        }
    }

    /**
     * @Then :it should not exist in the registry
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion)
    {
        expect($this->promotionRepository->find($promotion->getId()))->toBe(null);
    }

    /**
     * @Then promotion :promotion should still exist in the registry
     */
    public function promotionShouldStillExistInTheRegistry(PromotionInterface $promotion)
    {
        expect($this->promotionRepository->find($promotion->getId()))->toNotBe(null);
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedOfSuccess()
    {
        // Not applicable in the domain scope
    }
}
