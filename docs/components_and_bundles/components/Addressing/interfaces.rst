.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_addressing_model_address-interface:

AddressInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by models representing the customer's address.

.. note::
   This interface extends `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_addressing_model_country-interface:

CountryInterface
~~~~~~~~~~~~~~~~

This interfaces should be implemented by models representing a country.

.. note::
   This interface extends `ToggleableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/ToggleableInterface.php>`_.

.. _component_addressing_model_province-interface:

ProvinceInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a part of a country.

.. _component_addressing_model_zone-interface:

ZoneInterface
~~~~~~~~~~~~~

This interface should be implemented by models representing a single zone.

It also holds all the :doc:`/components_and_bundles/components/Addressing/zone_types`.

.. _component_addressing_model_zone-member-interface:

ZoneMemberInterface
~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that represent an area a specific
zone contains, e.g. all countries in the European Union.

Service Interfaces
------------------

.. _component_addressing_checker_restricted-zone-checker-interface:

RestrictedZoneCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

A service implementing this interface should be able to check
if given :ref:`component_addressing_model_address` is in a restricted zone.

.. _component_addressing_matcher_zone-matcher-interface:

ZoneMatcherInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by a service responsible of finding the best matching zone,
and all zones containing the provided :ref:`component_addressing_model_address`.
