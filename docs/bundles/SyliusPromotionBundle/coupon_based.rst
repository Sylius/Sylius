Coupon based promotions
=======================

Coupon based promotions require special needs that are covered by this documentation.

Coupon generator
----------------

``SyliusPromotionBundle`` provides a way of generating coupons for a promotion : the coupon generator. Provided as a service ``sylius.generator.promotion_coupon`` via the class ``Sylius\Bundle\PromotionBundle\Generator\CouponGenerator``, its goal is to generate unique coupon codes.


Coupon to code transformer
--------------------------

``SyliusPromotionBundle`` provides a way to transform a simple string code to a real ``Coupon`` object (and vice versa). This is done via the ``Sylius\Bundle\PromotionBundle\Form\DataTransformer\CouponToCodeTransformer`` class.

This data transformer is used by default with the ``Sylius\Bundle\PromotionBundle\Form\Type\CouponToCodeType`` form, provided as the service ``sylius.form.type.promotion_coupon_to_code``.

.. note::

    An example of integration of this form can be found in the ``Sylius\Bundle\CoreBundle\Form\Type\CartType`` class of ``Sylius/Standard-Edition``.
    

Coupon controller
-----------------

The ``Sylius\Bundle\PromotionBundle\Controller\CouponController`` provides an interface for easily generating new coupons.