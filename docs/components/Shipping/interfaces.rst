Interfaces
==========

Model Interfaces
----------------

.. _component_shipping_model_rule-interface:

RuleInterface
~~~~~~~~~~~~~

This interface should be implemented by class which will provide additional restriction for **ShippingMethod**.

.. note::
    For more detailed information go to `Sylius API RuleInterface`_.

.. _Sylius API RuleInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/RuleInterface.html

.. _component_shipping_model_shipment-interface:

ShipmentInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by class which will provide information about shipment like: state, shipping method
and so on. It also has a method for shipment tracking.

.. note::
    This interface extends the :ref:`component_shipping_model_shipping-subject-interface`.

    For more detailed information go to `Sylius API ShipmentInterface`_.

.. _Sylius API ShipmentInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShipmentInterface.html

.. _component_shipping_model_shipment-item-interface:

ShipmentItemInterface
~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by class responsible for connecting shippable object with proper shipment. It also
provides information about shipment state.

.. note::
    This interface extends the :ref:`component_shipping_model_shipping-subject-interface`.

    For more detailed information go to `Sylius API ShipmentItemInterface`_.

.. _Sylius API ShipmentItemInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShipmentItemInterface.html

ShippableInterface
~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing physical object which can by stored in a shop.

.. note::
    For more detailed information go to `Sylius API ShippableInterface`_.

.. _Sylius API ShippableInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippableInterface.html

.. _component_shipping_model_shipping-category-interface:

ShippingCategoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a shipping category and it is required if you want to classify
shipments and connect it with right shipment method.

.. note::
    This interface extends the :ref:`component_resource_model_code-aware-interface` and :ref:`component_resource_model_timestampable-interface`.

    For more detailed information go to `Sylius API ShippingCategoryInterface`_.

.. _Sylius API ShippingCategoryInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingCategoryInterface.html

.. _component_shipping_model_shipping-method-interface:

ShippingMethodInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface provides default requirements for system of matching shipping methods with shipments based on **ShippingCategory**
and allows to add a new restriction to a basic shipping method.

.. note::
    This interface extends the :ref:`component_resource_model_code-aware-interface`, :ref:`component_resource_model_timestampable-interface`
    and :ref:`component_shipping_model_shipping-method-translation-interface`.

    For more detailed information go to `Sylius API ShippingMethodInterface`_.

.. _Sylius API ShippingMethodInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingMethodInterface.html

.. _component_shipping_model_shipping-method-translation-interface:

ShippingMethodTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model responsible for keeping translation for **ShippingMethod** name.

.. note::
    For more detailed information go to `Sylius API ShippingMethodTranslationInterface`_.

.. _Sylius API ShippingMethodTranslationInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingMethodTranslationInterface.html

.. _component_shipping_model_shipping-subject-interface:

ShippingSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any object, which needs to be evaluated by default shipping calculators and rule checkers.

.. note::
    For more detailed information go to `Sylius API ShippingSubjectInterface`_.

.. _Sylius API ShippingSubjectInterface: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingSubjectInterface.html


Calculator interfaces
---------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

This interface provides basic methods for calculators. Every custom calculator should implement **CalculatorInterface** or extends
class **Calculator**, which has a basic implementation of methods from this interface.

.. note::
    For more detailed information go to `Sylius API CalculatorInterface`_.

.. _Sylius API CalculatorInterface: http://api.sylius.org/Sylius/Component/Shipping/Calculator/CalculatorInterface.html

DelegatingCalculatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any object, which will be responsible for delegating the calculation to a correct calculator instance.

.. note::
    For more detailed information go to `Sylius API DelegatingCalculatorInterface`_.

.. _Sylius API DelegatingCalculatorInterface: http://api.sylius.org/Sylius/Component/Shipping/Calculator/DelegatingCalculatorInterface.html

.. _component_shipping_calculator_registry-shipping-method-eligibility-checker-interface:

CalculatorRegistryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which will keep all calculators registered inside container.

.. note::
    For more detailed information go to `Sylius API CalculatorRegistryInterface`_.

.. _Sylius API CalculatorRegistryInterface: http://api.sylius.org/Sylius/Component/Shipping/Calculator/Registry/CalculatorRegistryInterface.html

Checker Interfaces
------------------

.. _component_shipping_checker_registry_rule-checker-registry-interface:

RuleCheckerRegistryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an service responsible for providing an information about available rule checkers.

.. note::
    For more detailed information go to `Sylius API RuleCheckerRegistryInterface`_.

.. _Sylius API RuleCheckerRegistryInterface: http://api.sylius.org/Sylius/Component/Shipping/Checker/Registry/RuleCheckerRegistryInterface.html

.. _component_shipping_checker_rule-checker-interface:

RuleCheckerInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which checks if a shipping subject meets the configured requirements.

.. note::
    For more detailed information go to `Sylius API RuleCheckerInterface`_.

.. _Sylius API RuleCheckerInterface: http://api.sylius.org/Sylius/Component/Shipping/Checker/RuleCheckerInterface.html


.. _component_shipping_checker_shipping-method-eligibility-checker-interface:

ShippingMethodEligibilityCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which checks if the given shipping subject is eligible for the shipping method rules.

.. note::
    For more detailed information go to `Sylius API ShippingMethodEligibilityCheckerInterface`_.

.. _Sylius API ShippingMethodEligibilityCheckerInterface: http://api.sylius.org/Sylius/Component/Shipping/Checker/ShippingMethodEligibilityCheckerInterface.html


Processor Interfaces
--------------------

ShipmentProcessorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which updates shipments and shipment items states.

.. note::
    For more detailed information go to `Sylius API ShipmentProcessorInterface`_.

.. _Sylius API ShipmentProcessorInterface: http://api.sylius.org/Sylius/Component/Shipping/Processor/ShipmentProcessorInterface.html

Resolver Interfaces
-------------------

ShippingMethodsResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be used to create object, which provides information about all allowed shipping methods
for given shipping subject.

.. note::
    For more detailed information go to `Sylius API ShippingMethodsResolverInterface`_.

.. _Sylius API ShippingMethodsResolverInterface: http://api.sylius.org/Sylius/Component/Shipping/Resolver/ShippingMethodsResolverInterface.html
