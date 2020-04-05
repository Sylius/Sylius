.. rst-class:: outdated

How promotions are applied?
===========================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

By using the :doc:`promotion eligibility checker </components_and_bundles/bundles/SyliusPromotionBundle/rule_checker>` and the :doc:`promotion applicator checker </components_and_bundles/bundles/SyliusPromotionBundle/action_applicator>` services, the promotion processor applies all the possible promotions on a subject.

The promotion processor is defined via the service ``sylius.promotion_processor`` which uses the class ``Sylius\Component\Promotion\Processor\PromotionProcessor``. Basically, it calls the method ``apply`` of the promotion applicator for all the active promotions that are eligible to the given subject.

