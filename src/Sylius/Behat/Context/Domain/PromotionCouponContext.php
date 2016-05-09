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
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Repository\CouponRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PromotionCouponContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CouponRepositoryInterface $couponRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, CouponRepositoryInterface $couponRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @When /^I try to delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iTryToDeleteCoupon(CouponInterface $coupon, PromotionInterface $promotion)
    {
        try {
            $promotion->removeCoupon($coupon);
            $this->couponRepository->remove($coupon);

            throw new \Exception(sprintf('Coupon "%s" has been removed, but it should not.', $coupon->getCode()));
        } catch(ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iDeleteCouponRelatedTo(CouponInterface $coupon, PromotionInterface $promotion)
    {
        $this->sharedStorage->set('coupon', $coupon);
        $this->couponRepository->remove($coupon);
        $promotion->removeCoupon($coupon);
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(CouponInterface $coupon)
    {
        expect($this->couponRepository->findOneBy(['code' => $coupon->getCode()]))->toBe(null);
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
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedOfSuccess()
    {
        // Not applicable in the domain scope
    }

    /**
     * @Given I am logged in as an administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        // Not applicable in the domain scope
    }
}
