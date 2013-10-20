<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Coupon handler.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class CouponHandler
{

    /**
     * @var EntityRepository
     */
    protected $couponRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param EntityRepository $couponRepository
     * @param Session $session
     */
    public function __construct(EntityRepository $couponRepository, Session $session)
    {
        $this->couponRepository = $couponRepository;
        $this->session = $session;
    }

    /**
     * Adds promotion coupon to the order if the coupon code exists.
     *
     * @param OrderInterface $order
     */
    public function handle(OrderInterface $order)
    {
        if (null != $code = $order->getCouponCode()) {
            if (null != $coupon = $this->couponRepository->findOneByCode($code)) {
                $order->setPromotionCoupon($coupon);
                $this->session->getFlashBag()->add('success', 'sylius.cart.summary.coupon_added');
            }
            else {
                $this->session->getFlashBag()->add('error', 'sylius.cart.summary.coupon_invalid');
            }
        }
    }
}