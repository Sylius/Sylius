How promotions are applied ?
============================

By using the :doc:`promotion eligibility checker </bundles/SyliusPromotionBundle/rule_checker>` and the :doc:`promotion applicator checker </bundles/SyliusPromotionBundle/action_applicator>` services, the promotion processor applies all the possible promotions on a subject.

The promotion processor is defined via the service ``sylius.promotion_processor`` which uses the class ``Sylius\Component\Promotion\Processor\PromotionProcessor``. Basically, it calls the method ``apply`` of the promotion applicator for all the active promotions that are eligible to the given subject.

