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
     * @When /^I delete ("([^"]+)" coupon)$/
     */
    public function iDeleteCoupon(CouponInterface $coupon)
    {
        try {
            $this->couponRepository->remove($coupon);
        } catch(ForeignKeyConstraintViolationException $exception) {
            $this->sharedStorage->set('exception', $exception);
        }
    }

    /**
     * @Then /^([^"]+) should no longer exist in the registry$/
     */
    public function couponShouldNotExistInTheRegistry(CouponInterface $coupon)
    {
        expect($this->couponRepository->find($coupon->getId()))->toBe(null);
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotified()
    {
        expect($this->sharedStorage->get('exception'))
            ->toReturnAnInstanceOf(ForeignKeyConstraintViolationException::class)
        ;
    }

    /**
     * @Then /^([^"]+) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(CouponInterface $coupon)
    {
        expect($this->couponRepository->find($coupon->getId()))->toNotBe(null);
    }
}
