.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_shipping_model_rule-interface:

RuleInterface
~~~~~~~~~~~~~

This interface should be implemented by class which will provide additional restriction for **ShippingMethod**.

.. _component_shipping_model_shipment-interface:

ShipmentInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by class which will provide information about shipment like: state, shipping method
and so on. It also has a method for shipment tracking.

.. note::
    This interface extends the :ref:`component_shipping_model_shipping-subject-interface`.

.. _component_shipping_model_shipment-item-interface:

ShipmentItemInterface
~~~~~~~~~~~~~~~~~~~~~

This interface is implemented by class responsible for connecting shippable object with proper shipment. It also
provides information about shipment state.

.. note::
    This interface extends the :ref:`component_shipping_model_shipping-subject-interface`.

ShippableInterface
~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing physical object which can by stored in a shop.

.. _component_shipping_model_shipping-category-interface:

ShippingCategoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a shipping category and it is required if you want to classify
shipments and connect it with right shipment method.

.. note::
    This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_shipping_model_shipping-method-interface:

ShippingMethodInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface provides default requirements for system of matching shipping methods with shipments based on **ShippingCategory**
and allows to add a new restriction to a basic shipping method.

.. note::
    This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_, `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_
    and :ref:`component_shipping_model_shipping-method-translation-interface`.

.. _component_shipping_model_shipping-method-translation-interface:

ShippingMethodTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model responsible for keeping translation for **ShippingMethod** name.

.. _component_shipping_model_shipping-subject-interface:

ShippingSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any object, which needs to be evaluated by default shipping calculators and rule checkers.

Calculator interfaces
---------------------

CalculatorInterface
~~~~~~~~~~~~~~~~~~~

This interface provides basic methods for calculators. Every custom calculator should implement **CalculatorInterface** or extends
class **Calculator**, which has a basic implementation of methods from this interface.

DelegatingCalculatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by any object, which will be responsible for delegating the calculation to a correct calculator instance.

.. _component_shipping_calculator_registry-shipping-method-eligibility-checker-interface:

CalculatorRegistryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which will keep all calculators registered inside container.

Checker Interfaces
------------------

.. _component_shipping_checker_registry_rule-checker-registry-interface:

RuleCheckerRegistryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an service responsible for providing an information about available rule checkers.

.. _component_shipping_checker_rule-checker-interface:

RuleCheckerInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which checks if a shipping subject meets the configured requirements.

.. _component_shipping_checker_shipping-method-eligibility-checker-interface:

ShippingMethodEligibilityCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which checks if the given shipping subject is eligible for the shipping method rules.

Processor Interfaces
--------------------

ShipmentProcessorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by an object, which updates shipments and shipment items states.

Resolver Interfaces
-------------------

ShippingMethodsResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be used to create object, which provides information about all allowed shipping methods
for given shipping subject.
