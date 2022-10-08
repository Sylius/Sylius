Interfaces
==========

Model Interfaces
----------------

.. _component_addressing_model_address-interface:

AddressInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by models representing the customer's address.

.. note::

   This interface extends `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

   For more detailed information go to `Sylius Addressing Component AddressInterface <https://github.com/Sylius/Addressing/blob/master/Model/AddressInterface.php>`_.

.. _component_addressing_model_country-interface:

CountryInterface
~~~~~~~~~~~~~~~~

This interfaces should be implemented by models representing a country.

.. note::

    This interface extends `ToggleableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/ToggleableInterface.php>`_
    and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

    For more detailed information go to `Sylius Addressing Component CountryInterface <https://github.com/Sylius/Addressing/blob/master/Model/CountryInterface.php>`_.

.. _component_addressing_model_province-interface:

ProvinceInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a part of a country.

.. note::

    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

    For more detailed information go to `Sylius Addressing Component ProvinceInterface <https://github.com/Sylius/Addressing/blob/master/Model/ProvinceInterface.php>`_.

.. _component_addressing_model_zone-interface:

ZoneInterface
~~~~~~~~~~~~~

This interface should be implemented by models representing a single zone.

It also holds all the :doc:`/components_and_bundles/components/Addressing/zone_types`.

.. note::
    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

    For more detailed information go to `Sylius Addressing Component ZoneInterface <https://github.com/Sylius/Addressing/blob/master/Model/ZoneInterface.php>`_.

.. _component_addressing_model_zone-member-interface:

ZoneMemberInterface
~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that represent an area a specific
zone contains, e.g. all countries in the European Union.

.. note::

    This interface extends `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

    For more detailed information go to `Sylius Addressing Component ZoneMemberInterface <https://github.com/Sylius/Addressing/blob/master/Model/ZoneMemberInterface.php>`_.


Service Interfaces
------------------

.. _component_addressing_checker_country-provinces-deletion-checker-interface:

CountryProvincesDeletionCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to check
if given :ref:`component_addressing_model_country` is deletable.

.. note::

   For more detailed information go to `Sylius Addressing Component CountryProvincesDeletionCheckerInterface <https://github.com/Sylius/Addressing/blob/master/Checker/CountryProvincesDeletionCheckerInterface.php>`_.

.. _component_addressing_checker_zone-deletion-checker-interface:

ZoneDeletionCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to check
if given :ref:`component_addressing_model_zone` is deletable.

.. note::

    For more detailed information go to `Sylius Addressing Component ZoneDeletionCheckerInterface <https://github.com/Sylius/Addressing/blob/master/Checker/ZoneDeletionCheckerInterface.php>`_.


.. _component_addressing_comparator_address-interface:

AddressComparatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to check
if given :ref:`component_addressing_model_address` is equal to other one.

.. note::

    For more detailed information go to `Sylius Addressing Component AddressComparatorInterface <https://github.com/Sylius/Addressing/blob/master/Comparator/AddressComparatorInterface.php>`_.

.. _component_addressing_converter_country-name-interface:

CountryNameConverterInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to convert
country name and given locale code to country code.

.. note::

    For more detailed information go to `Sylius Addressing Component CountryNameConverterInterface <https://github.com/Sylius/Addressing/blob/master/Converter/CountryNameConverterInterface.php>`_.

.. _component_addressing_matcher_zone-matcher-interface:

ZoneMatcherInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service responsible of finding the best matching zone,
and all zones containing the provided :ref:`component_addressing_model_address`.

.. note::

    For more detailed information go to `Sylius Addressing Component ZoneMatcherInterface <https://github.com/Sylius/Addressing/blob/master/Matcher/ZoneMatcherInterface.php>`_.
