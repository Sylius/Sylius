Coupon based promotions
=======================

Coupon based promotions require special needs that are covered by this documentation.

Coupon generator
----------------

``SyliusPromotionBundle`` provides a way of generating coupons for a promotion : the coupon generator.
Provided as a service ``sylius.promotion_coupon_generator`` via the class ``Sylius\Component\Promotion\Generator\PromotionCouponGenerator``, its goal is to generate unique coupon codes.

PromotionCoupon controller
--------------------------

The ``Sylius\Bundle\PromotionBundle\Controller\PromotionCouponController`` provides a method for generating new coupons.
