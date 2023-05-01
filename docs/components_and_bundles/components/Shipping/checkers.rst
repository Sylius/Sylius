.. rst-class:: outdated

Checkers
========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

ItemCountRuleChecker
--------------------

This class checks if item count exceeds (or at least is equal) the configured count.
An example about how to use it is on :ref:`component_shipping_checker-rule-checker-interface`.

.. note::
    This checker implements the :ref:`component_shipping_checker_rule-checker-interface`.

ShippingMethodEligibilityChecker
--------------------------------

This class checks if shipping method rules are capable of shipping given subject.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Rule;
    use Sylius\Component\Shipping\Model\ShippingMethod;
    use Sylius\Component\Shipping\Model\ShippingCategory;
    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
    use Sylius\Component\Shipping\Model\RuleInterface;
    use Sylius\Component\Shipping\Checker\ItemCountRuleChecker;
    use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityChecker;
    use Sylius\Component\Shipping\Checker\RuleCheckerInterface;
    use Sylius\Component\Registry\ServiceRegistry;

    $rule = new Rule();
    $rule->setConfiguration(array('count' => 0, 'equal' => true));
    $rule->setType(RuleInterface::TYPE_ITEM_COUNT);

    $shippingCategory = new ShippingCategory();
    $shippingCategory->setName('Regular');

    $hippingMethodTranslate = new ShippingMethodTranslation();
    $hippingMethodTranslate->setLocale('en');
    $hippingMethodTranslate->setName('First method');

    $shippingMethod = new ShippingMethod();
    $shippingMethod->setCategory($shippingCategory);
    $shippingMethod->setCurrentLocale('en');
    $shippingMethod->setFallbackLocale('en');
    $shippingMethod->addTranslation($hippingMethodTranslate);

    $shippingMethod->addRule($rule);

    $shippable = new ShippableObject();
    $shippable->setShippingCategory($shippingCategory);

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($shippable);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $ruleChecker = new ItemCountRuleChecker();

    $ruleCheckerRegistry = new ServiceRegistry(RuleCheckerInterface::class);
    $ruleCheckerRegistry->register(RuleInterface::TYPE_ITEM_COUNT, $ruleChecker);

    $methodEligibilityChecker = new ShippingMethodEligibilityChecker($ruleCheckerRegistry);

    ///returns true, because quantity of shipping item in shipment is equal as count in rule's configuration
    $methodEligibilityChecker->isEligible($shipment, $shippingMethod);

    // returns true, because the shippable object has the same category as shippingMethod
    // and shipping method has default category requirement
    $methodEligibilityChecker->isCategoryEligible($shipment, $shippingMethod);

.. caution::
    The method ``->register()`` throws `InvalidArgumentException`_.

.. note::
    This model implements the :ref:`component_shipping_checker_shipping-method-eligibility-checker-interface`.
.. _InvalidArgumentException: https://php.net/manual/en/class.invalidargumentexception.php
