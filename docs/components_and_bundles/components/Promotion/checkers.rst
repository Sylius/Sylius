.. rst-class:: outdated

Checkers
========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_promotion_checker_item-count-rule-checker:

ItemCountRuleChecker
--------------------

You can use it when your subject implements the :ref:`component_promotion_model_promotion-countable-subject-interface`:

.. code-block:: php

    <?php

    $itemCountChecker = new ItemCountRuleChecker();
    // a Subject that implements the CountablePromotionSubjectInterface
    $subject->setQuantity(3);

    $configuration = array('count' => 2);

    $itemCountChecker->isEligible($subject, $configuration); // returns true

.. _component_promotion_checker_item-total-rule-checker:

ItemTotalRuleChecker
--------------------

If your subject implements the :ref:`component_promotion_model_promotion-subject-interface` you can use it with this checker.

.. _component_promotion_checker_promotion-eligibility-checker:

.. code-block:: php

    <?php

    $itemTotalChecker = new ItemTotalRuleChecker();

    // a Subject that implements the PromotionSubjectInterface
    // Let's assume the subject->getSubjectItemTotal() returns 199

    $configuration = array('amount' => 199);

    $itemTotalChecker->isEligible($subject, $configuration); // returns true

