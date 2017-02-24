SyliusPromotionBundle
=====================

Promotions system for Symfony applications.

With minimal configuration you can introduce promotions and coupons into your project. The following types of promotions are available and **totally mixable**:

- percentage discounts
- fixed amount discounts
- promotions limited by time
- promotions limited by a maximum number of usages
- promotions based on coupons

This means you can for instance create the following promotions :

- 20$ discount for New Year orders having more than 3 items
- 8% discount for Christmas orders over 100 EUR
- first 3 orders have 100% discount
- 5% discount this week with the coupon code *WEEK5*
- 40€ discount with the code you have received by mail

.. toctree::
   :numbered:

   installation
   models
   rule_checker
   action_applicator
   applying_promotions
   coupon_based
   summary

Learn more
----------

* :doc:`Promotions in the Sylius platform </book/orders/promotions>` - concept documentation
