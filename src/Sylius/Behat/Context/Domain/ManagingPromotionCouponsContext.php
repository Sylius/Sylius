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
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingPromotionCouponsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PromotionCouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PromotionCouponRepositoryInterface $couponRepository
     */
    public function __construct(SharedStorageInterface $sharedStorage, PromotionCouponRepositoryInterface $couponRepository)
    {
        $this->sharedStorage = $sharedStorage;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iDeleteCoupon(PromotionCouponInterface $coupon, PromotionInterface $promotion)
    {
        $promotion->removeCoupon($coupon);
        $this->couponRepository->remove($coupon);
    }

    /**
     * @When /^I try to delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iTryToDeleteCoupon(PromotionCouponInterface $coupon, PromotionInterface $promotion)
    {
        try {
            $promotion->removeCoupon($coupon);
            $this->couponRepository->remove($coupon);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(PromotionCouponInterface $coupon)
    {
        Assert::null($this->couponRepository->findOneBy(['code' => $coupon->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), ForeignKeyConstraintViolationException::class);
    }

    /**
     * @Then /^([^"]+) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(PromotionCouponInterface $coupon)
    {
        Assert::notNull($this->couponRepository->find($coupon->getId()));
    }
}
