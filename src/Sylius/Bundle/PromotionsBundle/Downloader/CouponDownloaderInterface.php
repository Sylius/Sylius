<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Downloader;

use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface implemented by Coupon Downloader classes
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface CouponDownloaderInterface
{
    /**
     * Get response to download promotion coupons
     *
     * @param PromotionInterface $promotion
     * @return Response
     */
    public function getDownloadResponse(PromotionInterface $promotion);
}
