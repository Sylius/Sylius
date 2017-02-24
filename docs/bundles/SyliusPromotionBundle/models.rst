Models
======

All the models of this bundle are defined in ``Sylius\Component\Promotion\Model``.

PromotionRule
-------------

A ``PromotionRule`` is used to check if your order is eligible to the promotion. A promotion can have none, one or several rules. ``SyliusPromotionBundle`` comes with 2 types of rules :

 - cart quantity rule : quantity of the order is checked
 - item total rule : the amount of the order is checked

A rule is configured via the ``configuration`` attribute which is an array serialized into database. For cart quantity rules, you have to configure the ``count`` key, whereas the ``amount`` key is used for item total rules.
Configuration is always strict, which means, that if you set ``count`` to **4** for cart quantity rule, orders with equal or more than **4** quantity will be eligible.

PromotionAction
---------------

An ``PromotionAction`` defines the nature of the discount. Common actions are :

- percentage discount
- fixed amount discount

An action is configured via the ``configuration`` attribute which is an array serialized into database. For percentage discount actions, you have to configure the ``percentage`` key, whereas the ``amount`` key is used for fixed discount rules.

PromotionCoupon
---------------

A ``PromotionCoupon`` is a ticket having a ``code`` that can be exchanged for a financial discount. A promotion can have none, one or several coupons.

A coupon is considered as valid if the method ``isValid()`` returns ``true``. This method checks the number of times this coupon can be used (attribute ``usageLimit``), the number of times this has already been used (attribute ``used``) and the coupon expiration date (attribute ``expiresAt``). If ``usageLimit`` is not set, the coupon will be usable an unlimited times.

PromotionSubjectInterface
-------------------------

A ``PromotionSubjectInterface`` is the object you want to apply the promotion on. For instance, in Sylius Standard, a ``Sylius\Component\Core\Model\Order`` can be subject to promotions.

By implementing ``PromotionSubjectInterface``, your object will have to define the following methods :
- ``getPromotionSubjectItemTotal()`` should return the amount of your order
- ``getPromotionSubjectItemCount()`` should return the number of items of your order
- ``getPromotionCoupon()`` should return the coupon linked to your order. If you do not want to use coupon, simply return ``null``.

Promotion
---------

The ``Promotion`` is the main model of this bundle. A promotion has a ``name``, a ``description`` and :

- can have none, one or several rules
- should have at least one action to be effective
- can be based on coupons
- can have a limited number of usages by using the attributes ``usageLimit`` and ``used``. When ``used`` reaches ``usageLimit`` the promotion is no longer valid. If ``usageLimit`` is not set, the promotion will be usable an unlimited times.
- can be limited by time by using the attributes ``startsAt`` and ``endsAt``

