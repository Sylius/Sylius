How are rules checked ?
=======================

Everything related to this subject is located in ``Sylius\Component\Promotion\Checker``.

Rule checkers
-------------

New rules can be created by implementing ``RuleCheckerInterface``. This interface provides the method ``isEligible`` which aims to determine if the promotion subject respects the current rule or not.

I told you before that ``SyliusPromotionBundle`` ships with 2 types of rules : cart quantity rule and item total rule.

Cart quantity rule is defined via the service ``sylius.promotion_rule_checker.cart_quantity`` which uses the class ``CartQuantityRuleChecker``. The method ``isEligible`` checks here if the promotion subject has the minimum quantity (method ``getPromotionSubjectItemCount()`` of ``PromotionSubjectInterface``) required by the rule.

Item total rule is defined via the service ``sylius.promotion_rule_checker.item_total`` which uses the class ``ItemTotalRuleChecker``. The method ``isEligible`` checks here if the promotion subject has the minimum amount (method ``getPromotionSubjectItemTotal()`` of ``PromotionSubjectInterface``) required by the rule.


The promotion eligibility checker service
-----------------------------------------

To be eligible to a promotion, a subject must :

1. respect all the rules related to the promotion
2. respect promotion dates if promotion is limited by time
3. respect promotions usages count if promotion has a limited number of usages
4. if a coupon is provided with this order, it must be valid and belong to this promotion

The service ``sylius.promotion_eligibility_checker`` checks all these constraints for you with the method ``isEligible()``  which returns ``true`` or ``false``. This service uses the class ``CompositePromotionEligibilityChecker``.

