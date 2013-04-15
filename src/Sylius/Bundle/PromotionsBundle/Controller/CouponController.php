<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Coupon controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class CouponController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $promotionId = $this->getRequest()->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given');
        }

        $promotion = $this
            ->getPromotionController()
            ->findOr404(array('id' => $promotionId))
        ;

        $coupon = parent::createNew();
        $coupon->setPromotion($promotion);

        return $coupon;
    }

    /**
     * Get promotion controller.
     *
     * @return ResourceController
     */
    protected function getPromotionController()
    {
        return $this->get('sylius.controller.promotion');
    }
}
