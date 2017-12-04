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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CouponContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $couponRepository;

    /**
     * @param RepositoryInterface $couponRepository
     */
    public function __construct(
        RepositoryInterface $couponRepository
    ) {
        $this->couponRepository = $couponRepository;
    }

    /**
     * @Transform /^coupon "([^"]+)"$/
     * @Transform /^"([^"]+)" coupon$/
     * @Transform :coupon
     */
    public function getCouponByCode($couponCode)
    {
        $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);

        Assert::notNull(
            $coupon,
            sprintf('Coupon with code "%s" does not exist', $couponCode)
        );

        return $coupon;
    }
}
