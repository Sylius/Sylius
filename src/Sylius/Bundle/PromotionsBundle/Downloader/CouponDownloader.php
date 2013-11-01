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
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * CouponDownloader implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CouponDownloader implements CouponDownloaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDownloadResponse(PromotionInterface $promotion)
    {
        $file = fopen('php://memory', 'w');

        $header = array(
            'Coupon Code',
            'Expiration Date',
            'Usage Limit',
            'Used'
        );

        fputcsv($file, $header);

        foreach ($promotion->getCoupons() as $coupon) {

            $line = array(
                $coupon->getCode(),
                $coupon->getExpiresAt(),
                $coupon->getUsageLimit(),
                $coupon->getUsed()
            );

            fputcsv($file, $line);
        }

        fseek($file, 0);

        $response = new Response(stream_get_contents($file));
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $this->getFilename($promotion));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Convenience method to override for custom file name
     *
     * @param PromotionInterface $promotion
     * @return string
     */
    protected function getFilename(PromotionInterface $promotion)
    {
        return sprintf('%s-Coupons-%s.csv', $promotion->getName(), date('m-d-Y'));
    }
}
